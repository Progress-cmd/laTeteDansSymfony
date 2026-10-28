<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class MenuController extends AbstractController
{
    public function _menu(?string $currentRoute = null): Response
    {
        $links = [
            ['route' => 'app_home', 'label' => 'Accueil'],
            ['route' => 'app_artistes', 'label' => 'Artistes'],
            ['route' => 'app_lieux', 'label' => 'Lieux'],
            ['route' => 'app_techniques', 'label' => 'Techniques'],
        ];

        return $this->render('menu/_menu.html.twig', [
            'currentRoute' => $currentRoute,
            'links' => $links,
        ]);
    }
}
