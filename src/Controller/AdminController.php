<?php
namespace App\Controller;

use App\Repository\RegistrationRepository;
use App\Repository\SummitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(RegistrationRepository $repo, SummitRepository $summitRepo): Response
    {
        $summit = $summitRepo->findOneBy(['isActive' => true]);
        $registrations = $repo->findBy(['summit' => $summit], ['registeredAt' => 'DESC']);

        return $this->render('admin/index.html.twig', [
            'registrations' => $registrations,
            'summit' => $summit,
        ]);
    }

    #[Route('/admin/edit/{id}', name: 'app_admin_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, RegistrationRepository $repo, EntityManagerInterface $em): Response
    {
        $registration = $repo->find($id);
        $form = $this->createForm(\App\Form\RegistrationFormType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'registration' => $registration,
        ]);
    }

    #[Route('/admin/delete/{id}', name: 'app_admin_delete')]
    public function delete(int $id, RegistrationRepository $repo, EntityManagerInterface $em): Response
    {
        $registration = $repo->find($id);
        if ($registration) {
            $em->remove($registration);
            $em->flush();
        }
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/export', name: 'app_admin_export')]
    public function export(RegistrationRepository $repo, SummitRepository $summitRepo): Response
    {
        $summit = $summitRepo->findOneBy(['isActive' => true]);
        $registrations = $repo->findBy(['summit' => $summit]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['ID', 'First Name', 'Last Name', 'Email', 'Company', 'Meal', 'Ticket No', 'Registered At'], null, 'A1');

        $row = 2;
        foreach ($registrations as $r) {
            $sheet->fromArray([
                $r->getId(),
                $r->getFirstName(),
                $r->getLastName(),
                $r->getEmail(),
                $r->getCompany(),
                $r->getMealPreference(),
                $r->getTicketNumber(),
                $r->getRegisteredAt()->format('d.m.Y H:i'),
            ], null, 'A' . $row++);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'registrations.xlsx';

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return new Response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    #[Route('/admin/attendance-pdf', name: 'app_admin_attendance')]
    public function attendancePdf(RegistrationRepository $repo, SummitRepository $summitRepo): Response
    {
        $summit = $summitRepo->findOneBy(['isActive' => true]);
        $registrations = $repo->findBy(['summit' => $summit]);

        $rows = '';
        foreach ($registrations as $r) {
            $rows .= '
            <tr>
                <td>' . $r->getTicketNumber() . '</td>
                <td>' . $r->getFirstName() . ' ' . $r->getLastName() . '</td>
                <td>' . $r->getEmail() . '</td>
                <td>' . ($r->getCompany() ?? '') . '</td>
                <td>' . ucfirst($r->getMealPreference()) . '</td>
                <td style="text-align:center;">□</td>
            </tr>';
        }

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; margin: 30px; font-size: 12px; }
                .header { background: #1c2b4a; color: white; padding: 15px 20px; margin-bottom: 20px; }
                .header h1 { color: #f0a500; margin: 0; font-size: 18px; }
                .header p { margin: 5px 0 0 0; font-size: 11px; color: #ccc; }
                .orange-bar { background: #f0a500; height: 4px; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th { background: #1c2b4a; color: white; padding: 8px 10px; text-align: left; font-size: 11px; }
                td { padding: 7px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
                tr:nth-child(even) { background: #f9f9f9; }
                .footer { margin-top: 20px; font-size: 10px; color: #999; text-align: center; }
                .summary { margin-bottom: 15px; font-size: 11px; color: #333; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>International School of Management</h1>
                <p>Attendance List — ' . ($summit ? $summit->getTitle() : '') . '</p>
            </div>
            <div class="orange-bar"></div>
            <div class="summary">
                <strong>Campus:</strong> ' . ($summit ? $summit->getLocation()->getCity() . ' — ' . $summit->getLocation()->getCampusName() : '') . ' &nbsp;|&nbsp;
                <strong>Date:</strong> ' . ($summit ? $summit->getLocation()->getEvenDate()->format('d.m.Y') : '') . ' &nbsp;|&nbsp;
                <strong>Total:</strong> ' . count($registrations) . ' registrations
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Ticket No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Meal</th>
                        <th>Present ✓</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $rows . '
                </tbody>
            </table>
            <div class="footer">
                International School of Management | en.ism.de | Printed: ' . date('d.m.Y H:i') . '
            </div>
        </body>
        </html>';

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="attendance-list.pdf"',
        ]);
    }

    #[Route('/admin/nametags-pdf', name: 'app_admin_nametags')]
    public function nametagsPdf(RegistrationRepository $repo, SummitRepository $summitRepo): Response
    {
        $summit = $summitRepo->findOneBy(['isActive' => true]);
        $registrations = $repo->findBy(['summit' => $summit]);

        $tags = '';
        foreach ($registrations as $r) {
            $tags .= '
            <div class="nametag">
                <div class="nametag-top">ISM Summit 2026</div>
                <div class="nametag-name">' . $r->getFirstName() . '<br>' . $r->getLastName() . '</div>
                <div class="nametag-company">' . ($r->getCompany() ?? '') . '</div>
                <div class="nametag-ticket">' . $r->getTicketNumber() . '</div>
            </div>';
        }

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; margin: 10px; }
                .nametag {
                    width: 220px;
                    height: 140px;
                    border: 2px solid #1c2b4a;
                    display: inline-block;
                    margin: 8px;
                    vertical-align: top;
                    text-align: center;
                    page-break-inside: avoid;
                    overflow: hidden;
                }
                .nametag-top {
                    background: #1c2b4a;
                    color: #f0a500;
                    font-size: 10px;
                    font-weight: bold;
                    padding: 5px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .nametag-name {
                    font-size: 18px;
                    font-weight: bold;
                    color: #1c2b4a;
                    padding: 15px 10px 5px 10px;
                    line-height: 1.3;
                }
                .nametag-company {
                    font-size: 11px;
                    color: #555;
                    padding: 0 10px;
                }
                .nametag-ticket {
                    font-size: 9px;
                    color: #999;
                    padding: 8px;
                    border-top: 1px solid #eee;
                    margin-top: 8px;
                }
            </style>
        </head>
        <body>
            ' . $tags . '
        </body>
        </html>';

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="name-tags.pdf"',
        ]);
    }
}