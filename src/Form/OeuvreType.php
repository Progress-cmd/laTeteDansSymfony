<?php

namespace App\Form;

use App\Entity\Artistes;
use App\Entity\Lieux;
use App\Entity\Oeuvre;
use App\Entity\Techniques;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OeuvreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('date_creation', null, [
                'label' => 'Date de création',
            ])
            ->add('description', null, [
                'required' => false,
            ])
            ->add('est_detruite', null, [
                'label' => 'Détruite',
                'required' => false,
            ])
            ->add('oeuvre_recouverte', EntityType::class, [
                'label' => 'Œuvre recouverte',
                'class' => Oeuvre::class,
                'choice_label' => 'titre',
                'required' => false,
            ])
            ->add('lieux', EntityType::class, [
                'label' => 'Lieu',
                'class' => Lieux::class,
                'choice_label' => 'nom',
                'required' => false,
            ])
            ->add('artistes', EntityType::class, [
                'class' => Artistes::class,
                'choice_label' => 'pseudonyme',
                'multiple' => true,
            ])
            ->add('techniques', EntityType::class, [
                'class' => Techniques::class,
                'choice_label' => 'nom',
                'multiple' => true,
            ])
            ->add('enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Oeuvre::class,
        ]);
    }
}
