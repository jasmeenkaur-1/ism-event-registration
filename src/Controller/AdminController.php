<?php
namespace App\Controller;

use App\Entity\Registration;
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
}