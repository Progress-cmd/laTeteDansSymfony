<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\PhotoType;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/photos')]
final class PhotoController extends AbstractController
{
    #[Route(name: 'app_photos')]
    public function index(PhotoRepository $photoRepository): Response
    {
        return $this->render('photo/index.html.twig', [
            'photos' => $photoRepository->findAll(),
        ]);
    }

    #[Route('/ajouter-photo', name: 'app_ajouter_photos')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($photo);
            $entityManager->flush();

            return $this->redirectToRoute('app_photos');
        }

        return $this->render('photo/ajouter.html.twig', [
            'photo' => $photo,
            'formulaire' => $form,
        ]);
    }

    #[Route('/afficher-photo/{id}', name: 'app_afficher_photos')]
    public function show(Photo $photo): Response
    {
        return $this->render('photo/afficher.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[Route('/modifier-photo/{id}', name: 'app_modifier_photos')]
    public function edit(Request $request, Photo $photo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_photos');
        }

        return $this->render('photo/modifier.html.twig', [
            'photo' => $photo,
            'formulaire' => $form,
        ]);
    }

    #[Route('/supprimer-photo/{id}', name: 'app_supprimer_photos')]
    public function delete(Request $request, Photo $photo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($photo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_photos');
    }
}
