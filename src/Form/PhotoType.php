<?php

namespace App\Form;

use App\Entity\Benevole;
use App\Entity\Oeuvre;
use App\Entity\Photo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // L'image est obligatoire à l'ajout, facultative à la modification (on garde l'ancienne)
        $nouvellePhoto = null === ($options['data'] ?? null)?->getId();

        $builder
            ->add('date_prise_vue')
            ->add('nom_fichier', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => $nouvellePhoto,
                'constraints' => array_filter([
                    $nouvellePhoto ? new NotNull(message: 'Choisissez une image.') : null,
                    new Image(maxSize: '2M'),
                ]),
            ])
            ->add('etat_oeuvre')
            ->add('benevole', EntityType::class, [
                'class' => Benevole::class,
                'choice_label' => 'email',
            ])
            ->add('oeuvre', EntityType::class, [
                'class' => Oeuvre::class,
                'choice_label' => 'titre',
                'required' => false,
            ])
            ->add('enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
