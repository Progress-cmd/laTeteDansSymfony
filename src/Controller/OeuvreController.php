<?php

namespace App\Controller;

use App\Entity\Oeuvre;
use App\Form\OeuvreType;
use App\Repository\OeuvreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/oeuvres')]
final class OeuvreController extends AbstractController
{
    #[Route(name: 'app_oeuvres')]
    public function index(OeuvreRepository $oeuvreRepository): Response
    {
        return $this->render('oeuvre/index.html.twig', [
            'oeuvres' => $oeuvreRepository->findAll(),
        ]);
    }

    #[Route('/ajouter-oeuvre', name: 'app_ajouter_oeuvres')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $oeuvre = new Oeuvre();
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($oeuvre);
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvres');
        }

        return $this->render('oeuvre/ajouter.html.twig', [
            'oeuvre' => $oeuvre,
            'formulaire' => $form,
        ]);
    }

    #[Route('/afficher-oeuvre/{id}', name: 'app_afficher_oeuvres')]
    public function show(Oeuvre $oeuvre): Response
    {
        return $this->render('oeuvre/afficher.html.twig', [
            'oeuvre' => $oeuvre,
        ]);
    }

    #[Route('/modifier-oeuvre/{id}', name: 'app_modifier_oeuvres')]
    public function edit(Request $request, Oeuvre $oeuvre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvres');
        }

        return $this->render('oeuvre/modifier.html.twig', [
            'oeuvre' => $oeuvre,
            'formulaire' => $form,
        ]);
    }
}
