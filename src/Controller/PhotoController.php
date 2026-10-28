<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\PhotoType;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
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
            // Il faut gérer la photo
            $this->ajouterLaPhoto($form, $photo);
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

        // Je garde le nom de l'ancienne photo
        $anciennePhoto = $photo->getNomFichier();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nouvellePhoto = $form->get('nom_fichier')->getData();

            if ($nouvellePhoto) {
                // Nouvelle image envoyée : je supprime l'ancienne, puis j'enregistre la nouvelle
                $this->supprimerLaPhoto($photo, $anciennePhoto);
                $this->ajouterLaPhoto($form, $photo);
            } else {
                // Pas de nouvelle image : je conserve l'ancienne
                $photo->setNomFichier($anciennePhoto);
            }

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
            $this->supprimerLaPhoto($photo);
            $entityManager->remove($photo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_photos');
    }

    private function ajouterLaPhoto(FormInterface $form, Photo $photo): void
    {
        $fichier = $form->get('nom_fichier')->getData();
        if ($fichier) {
            $nomOriginal = pathinfo($fichier->getClientOriginalName(), PATHINFO_BASENAME);
            $fichier->move($this->getParameter('kernel.project_dir').'/public/Photos', $nomOriginal);
            // Je remplis ce champ à la main puisque j'ai mis mapped=false dans PhotoType
            $photo->setNomFichier('Photos/'.$nomOriginal);
        }
    }

    private function supprimerLaPhoto(Photo $photo, ?string $nomPhoto = null): void
    {
        $nomPhoto = $nomPhoto ?? $photo->getNomFichier();
        if ($nomPhoto) {
            $chemin = $this->getParameter('kernel.project_dir').'/public/'.$nomPhoto;

            $filesystem = new Filesystem();
            if ($filesystem->exists($chemin)) {
                $filesystem->remove($chemin);
            }
        }
    }
}
