<?php

namespace App\Controller;

use App\Entity\Artistes;
use App\Form\ArtistesType;
use App\Repository\ArtistesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/artistes')]
final class ArtistesController extends AbstractController
{
    #[Route(name: 'app_artistes')]
    public function index(ArtistesRepository $artistesRepository): Response
    {
        return $this->render('artistes/index.html.twig', [
            'artistes' => $artistesRepository->findAll(),
        ]);
    }

    #[Route('/creer-artiste', name: 'app_creer_artistes')]
    public function creer(Request $request, EntityManagerInterface $entityManager): Response
    {
        $artiste = new Artistes();
        $form = $this->createForm(ArtistesType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($artiste);
            $entityManager->flush();

            return $this->redirectToRoute('app_artistes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artistes/creer.html.twig', [
            'artiste' => $artiste,
            'formulaire' => $form,
        ]);
    }

    #[Route('/afficher-artiste/{id}', name: 'app_afficher_artistes')]
    public function afficher(Artistes $artiste): Response
    {
        return $this->render('artistes/afficher.html.twig', [
            'artiste' => $artiste,
        ]);
    }

    #[Route('/modifier-artiste/{id}', name: 'app_modifier_artistes')]
    public function modifier(Request $request, Artistes $artiste, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArtistesType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_artistes');
        }

        return $this->render('artistes/modifier.html.twig', [
            'artiste' => $artiste,
            'formulaire' => $form,
        ]);
    }

    #[Route('/supprimer-artiste/{id}', name: 'app_supprimer_artistes', methods: ['POST'])]
    public function supprimer(Request $request, Artistes $artiste, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artiste->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($artiste);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_artistes', [], Response::HTTP_SEE_OTHER);
    }
}
