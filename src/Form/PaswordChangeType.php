<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class PaswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if ($options['change'] == true) {
            $builder
                ->add('oldPassword', PasswordType::class, [
                    'label' => 'Mot de passe actuel',
                    'mapped' => false,
                    'attr' => [
                        'placeholder' => ''
                    ]
                ])
                ->add('newPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'invalid_message' => 'Les mots de passe ne sont pas identiques',
                    'label' => 'Nouveau mot de passe',
                    'options' => ['attr' => ['class' => 'password']],
                    'required' => true,
                    'first_options' => [
                        'label' => 'Nouveau mot de passe',
                        'attr' => [
                            'placeholder' => ''
                        ],
                        'constraints' => [
                            new Regex([
                                'pattern' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                                'message' => "Le mot de passe doit contenir 1 majuscule, 1 minuscule, 1 chiffre et un caractère spécial"
                            ])
                        ],
                    ],
                    'second_options' => [
                        'label' => 'Confirmation du nouveau mot de passe',
                        'attr' => [
                            'placeholder' => ''
                        ]
                    ]
                ])
                ->add('submit', SubmitType::class, [
                    'label' => "Mettre à jour"
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'change' => false,

        ]);
    }
}
