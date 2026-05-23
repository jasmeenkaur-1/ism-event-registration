<?php
namespace App\Controller;

use App\Entity\Registration;
use App\Form\RegistrationFormType;
use App\Service\RegistrationService;
use App\Service\PdfService;
use App\Repository\SummitRepository;
use App\Repository\RegistrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, SummitRepository $summitRepository, RegistrationService $registrationService): Response
    {
        $summit = $summitRepository->findOneBy(['isActive' => true]);
        if (!$summit) {
            return $this->render('registration/closed.html.twig');
        }

        $registration = new Registration();
        $form = $this->createForm(RegistrationFormType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $registrationService->register($registration, $summit);
            if ($result['success']) {
                return $this->redirectToRoute('app_success', ['id' => $result['id']]);
            }
            $this->addFlash('error', 'Sorry, no seats available!');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
            'summit' => $summit,
        ]);
    }

    #[Route('/success/{id}', name: 'app_success')]
    public function success(int $id, RegistrationRepository $repo): Response
    {
        $registration = $repo->find($id);
        return $this->render('registration/success.html.twig', [
            'registration' => $registration,
        ]);
    }

    #[Route('/ticket/{id}', name: 'app_ticket')]
    public function downloadTicket(int $id, RegistrationRepository $repo, PdfService $pdfService): Response
    {
        $registration = $repo->find($id);
        $pdf = $pdfService->generateTicket($registration);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ticket-' . $registration->getTicketNumber() . '.pdf"',
        ]);
    }
}