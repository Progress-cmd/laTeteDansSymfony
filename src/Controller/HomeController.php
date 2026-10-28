<?php

namespace App\Controller;

use App\Repository\OeuvreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(OeuvreRepository $oeuvreRepository): Response
    {
        return $this->render('home/index.html.twig', [
            // Les œuvres les plus récentes en premier
            'oeuvres' => $oeuvreRepository->findBy([], ['date_creation' => 'DESC']),
        ]);
    }
}
