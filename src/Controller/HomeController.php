<?php
namespace App\Controller;

use App\Repository\SummitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SummitRepository $summitRepository): Response
    {
        $summit = $summitRepository->findOneBy(['isActive' => true]);
        return $this->render('home/index.html.twig', [
            'summit' => $summit,
        ]);
    }
}