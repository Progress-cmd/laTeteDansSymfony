<?php

namespace App\Controller;

use App\Entity\Techniques;
use App\Form\TechniquesType;
use App\Repository\TechniquesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/techniques')]
final class TechniquesController extends AbstractController
{
    #[Route(name: 'app_techniques')]
    public function index(TechniquesRepository $techniquesRepository): Response
    {
        return $this->render('techniques/index.html.twig', [
            'techniques' => $techniquesRepository->findAll(),
        ]);
    }

    #[Route('/ajouter-techniques', name: 'app_ajouter_techniques')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $technique = new Techniques();
        $form = $this->createForm(TechniquesType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($technique);
            $entityManager->flush();

            return $this->redirectToRoute('app_techniques');
        }

        return $this->render('techniques/ajouter.html.twig', [
            'technique' => $technique,
            'formulaire' => $form,
        ]);
    }

    #[Route('/afficher-techniques/{id}', name: 'app_afficher_techniques')]
    public function show(Techniques $technique): Response
    {
        return $this->render('techniques/afficher.html.twig', [
            'technique' => $technique,
        ]);
    }

    #[Route('/modifier-technique/{id}', name: 'app_modifier_techniques')]
    public function edit(Request $request, Techniques $technique, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TechniquesType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_techniques');
        }

        return $this->render('techniques/modifier.html.twig', [
            'technique' => $technique,
            'formulaire' => $form,
        ]);
    }

    #[Route('/supprimer-techniques/{id}', name: 'app_supprimer_techniques')]
    public function delete(Request $request, Techniques $technique, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$technique->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($technique);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_techniques');
    }
}
