<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\SubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['add'] == true) {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Saisir le nom du menu'
                ]

            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Saisir la description du menu'
                ]

            ])
            ->add('protein', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'placeholder' => 'Séléctionner la protéine du menu',
                'choices' => [
                    'Boeuf' => 'Boeuf',
                    'Poisson' => 'Poisson',
                    'Poulet' => 'Poulet',
                    'Veggie'=>  'Veggie'
                ],
               
            ])
            ->add('price', NumberType::class, [
                'required' => false,
                'label' => false,
                'invalid_message' => 'Le type est incorrect',
                'attr' => [
                    'placeholder' => 'Saisir le prix du menu'
                ]
            ])
            ->add('picture', FileType::class, [
                'required' => false,
                'label' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            "image/png",
                            "image/jpg",
                            "image/jpeg",
                            "image/svg"

                        ],
                        'mimeTypesMessage' => 'Extensions autorisées: PNG, JPG, JPEG, SVG'
                    ])
                ]
            ])
            ->add('subCategory', EntityType::class, [
                'class' => SubCategory::class,
                'placeholder' => 'Choisissez une sous-catégorie dans laquelle placer ce menu',
                'choice_label' => 'title',
                'required' => false,
                'label' => false,
                ]) 
                   
                ->add('Enregistrer', SubmitType::class);
        
        }elseif ($options['edit'] == true) {
            $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Saisir le nom du menu'
                ]

            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Saisir la description du menu'
                ]

            ])
            ->add('protein', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'placeholder' => 'Séléctionner la protéine du menu',
                'choices' => [
                    'Boeuf' => 'Boeuf',
                    'Poisson' => 'Poisson',
                    'Poulet' => 'Poulet',
                    'Veggie'=>  'Veggie'
                ]

            ])
            ->add('price', NumberType::class, [
                'required' => false,
                'label' => false,
                'invalid_message' => 'Le type est incorrect',
                'attr' => [
                    'placeholder' => 'Saisir le prix du menu'
                ]
            ])
            ->add('editPicture', FileType::class, [
                'required' => false,
                'label' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            "image/png",
                            "image/jpg",
                            "image/jpeg",
                            "image/svg"

                        ],
                        'mimeTypesMessage' => 'Extensions autorisées: PNG, JPG, JPEG, SVG'
                    ])
                ]
            ])
            ->add('subCategory', EntityType::class, [
                'class' => SubCategory::class,
                'placeholder' => 'Choisissez une sous-catégorie dans laquelle placer ce menu',
                'choice_label' => 'title',
                'required' => false,
                'label' => false,
                ]) 
                   
                ->add('Enregistrer', SubmitType::class);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'add' => false,
            'edit' => false
        ]);
    }
}
