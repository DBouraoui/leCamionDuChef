<?php

namespace App\Form;

use App\Entity\FindUs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FinUsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('day', ChoiceType::class, [
                'choices' => [
                    'Lundi' => 'Lundi',
                    'Mardi' => 'Mardi',
                    'Mercredi' => 'Mercredi',
                    'Jeudi' => 'Jeudi',
                    'Vendredi' => 'Vendredi',
                    'Samedi' => 'Samedi',
                    'Dimanche' => 'Dimanche',
                ],
                'placeholder' => 'Choisissez un jour',
                'required' => true,
                'label' => 'Jour de la semaine'
            ])
            ->add('morningTime', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: 11h30 - 14h30'
                ],
                'label' => 'Horaires du midi'
            ])
            ->add('morningLocalisation', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: Place de la République, Paris 11e'
                ],
                'label' => 'Emplacement du midi'
            ])
            ->add('eveningTime', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: 18h30 - 21h30'
                ],
                'label' => 'Horaires du soir'
            ])
            ->add('eveningLocalisation', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: Parc de la Villette, Paris 19e'
                ],
                'label' => 'Emplacement du soir'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FindUs::class,
        ]);
    }
}
