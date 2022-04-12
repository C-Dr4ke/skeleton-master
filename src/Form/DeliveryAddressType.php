<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Entrez votre prénom']
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille',
                'attr' => ['placeholder' => 'Entrez votre nom']
            ])
            ->add('streetNumber', NumberType::class, [
                'label' => 'N° de rue',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisissez votre numéro de rue'
                ]
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisissez votre nom de rue'
                ]
            ])
            ->add('zipCode', NumberType::class, [
                'label' => 'Code Postal',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisissez votre code postal'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisissez votre ville'
                ]
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisissez votre pays'
                ]
            ])
            ->add('phone', NumberType::class, [
                'label' => 'N° de Téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisissez votre numéro de téléphone'
                ]
            ])
            ->add('informations', TextType::class, [
                'label' => 'Saississez les informations que vous souhaitez transmettre pour la livraison',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisissez les informations à fournir pour la livraison'
                ]
            ])
                
            ->add('submit', SubmitType::class, [
                'label' => "Enregistrer cette addresse"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
