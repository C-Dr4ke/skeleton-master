<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        if ($options['add'] == true) {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Entrez votre prénom']
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille',
                'attr' => ['placeholder' => 'Entrez votre nom']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Entrez votre email']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['placeholder' => 'Entrez votre mot de passe']

            ])


            ->add('submit', SubmitType::class, [
                'label' => 'Inscription',
                'attr' => ['class' => 'btn mb-2 mb-md-0 btn-outline-dark btn-block']
            ]);
    }elseif ($options['edit'] == true) {
        $builder 
        ->add('firstname', TextType::class, [
            'label' => 'Prénom',
            'attr' => ['placeholder' => 'Entrez votre prénom']
        ])
        ->add('lastname', TextType::class, [
            'label' => 'Nom de famille',
            'attr' => ['placeholder' => 'Entrez votre nom']
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => ['placeholder' => 'Entrez votre email']
        ])
        ->add('streetNumber', NumberType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'placeholder' => 'Saisissez votre numéro de rue'
            ]
        ])
        ->add('street', TextType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'placeholder' => 'Saisissez votre nom de rue'
            ]
        ])
        ->add('zipCode', NumberType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'placeholder' => 'Saisissez votre code postal'
            ]
        ])
        ->add('city', TextType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'placeholder' => 'Saisissez votre ville'
            ]
        ])
        ->add('phone', NumberType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'placeholder' => 'Saisissez votre numéro de téléphone'
            ]
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Modifier mon profil',
            'attr' => ['class' => 'btn mb-2 mb-md-0 btn-outline-dark btn-block']
        ]);
    }
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'add' => false,
            'edit' => false
        ]);
    }
}
