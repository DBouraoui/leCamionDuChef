<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('phone')
            ->add('message')
            ->add('subject',ChoiceType::class, [
        'choices' => [
            'Sélectionnez un sujet' => '',
            'Réservation pour événement' => 'reservation',
            'Demande d\'information' => 'information',
            'Suggestion' => 'suggestion',
            'Réclamation' => 'reclamation',
            'Autre' => 'autre'
        ],
        'placeholder' => false,
    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
