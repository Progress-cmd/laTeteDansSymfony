<?php

namespace App\Form;

use App\Entity\Artistes;
use App\Entity\Oeuvre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArtistesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudonyme')
            ->add('nom_reel', null, [
                'label' => 'Nom réel',
            ])
            ->add('bio')
            ->add('nationalite', null, [
                'label' => 'Nationalité',
            ])
            ->add('oeuvres', EntityType::class, [
                'label' => 'Œuvres',
                'class' => Oeuvre::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artistes::class,
        ]);
    }
}
