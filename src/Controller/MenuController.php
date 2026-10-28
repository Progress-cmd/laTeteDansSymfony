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
        ];

        // Les autres pages ne sont visibles que si on est connecté
        if ($this->isGranted('ROLE_USER')) {
            $links[] = ['route' => 'app_artistes', 'label' => 'Artistes'];
            $links[] = ['route' => 'app_lieux', 'label' => 'Lieux'];
            $links[] = ['route' => 'app_techniques', 'label' => 'Techniques'];
            $links[] = ['route' => 'app_oeuvres', 'label' => 'Œuvres'];
            $links[] = ['route' => 'app_photos', 'label' => 'Photos'];
        }

        return $this->render('menu/_menu.html.twig', [
            'currentRoute' => $currentRoute,
            'links' => $links,
        ]);
    }
}
