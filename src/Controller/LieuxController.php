<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Form\LieuxType;
use App\Repository\LieuxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/lieux')]
final class LieuxController extends AbstractController
{
    #[Route(name: 'app_lieux')]
    public function index(LieuxRepository $lieuxRepository): Response
    {
        return $this->render('lieux/index.html.twig', [
            'lieuxes' => $lieuxRepository->findAll(),
        ]);
    }

    #[Route('/ajouter-lieux', name: 'app_ajouter_lieux')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieux = new Lieux();
        $form = $this->createForm(LieuxType::class, $lieux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieux);
            $entityManager->flush();

            return $this->redirectToRoute('app_lieux');
        }

        return $this->render('lieux/ajouter.html.twig', [
            'lieux' => $lieux,
            'formulaire' => $form,
        ]);
    }

    #[Route('/afficher-lieux/{id}', name: 'app_afficher_lieux')]
    public function show(Lieux $lieux): Response
    {
        return $this->render('lieux/afficher.html.twig', [
            'lieux' => $lieux,
        ]);
    }

    #[Route('/modifier-lieux/{id}', name: 'app_modifier_lieux')]
    public function edit(Request $request, Lieux $lieux, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LieuxType::class, $lieux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_lieux');
        }

        return $this->render('lieux/modifier.html.twig', [
            'lieux' => $lieux,
            'formulaire' => $form,
        ]);
    }

    #[Route('/supprimer-lieux/{id}', name: 'app_supprimer_lieux')]
    public function delete(Request $request, Lieux $lieux, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lieux->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($lieux);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lieux');
    }
}
