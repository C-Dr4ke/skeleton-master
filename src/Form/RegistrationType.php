<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

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
            ->add('password', RepeatedType::class, [
                'type'=>PasswordType::class,
                'mapped'=>false,
                'invalid_message'=>'Les mots de passe ne sont pas identiques', 
                'label'=>'Nouveau mot de passe',
                'options'=>['attr'=>['class'=>'password']],
                'required'=>true,
                'first_options'=>[
                    'label'=>'Nouveau mot de passe',
                    'attr'=>[
                        'placeholder'=>''
                    ],
                        'constraints'=>[
                            new Regex([
                                'pattern' =>'/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                                'message' =>"Le mot de passe doit contenir 1 majuscule, 1 minuscule, 1 chiffre et un caractère spécial"
                            ])
                            ],
                ],
                'second_options'=>['label'=>'Confirmation du nouveau mot de passe',
                'attr'=>[
                    'placeholder'=>''
                    ]
                ]
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
