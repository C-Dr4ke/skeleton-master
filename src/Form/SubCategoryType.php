<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\SubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title',TextType::class,[
            'label'=>false,
            'required'=>false,
            'attr'=>[
                'placeholder'=>'Saisissez le nom de la sous-catégorie'
            ]
        ])
        ->add('Category',EntityType::class,[
            'label'=>false,
            'required'=>false,
            'placeholder'=>'Saisissez le nom de la catégorie en relation',
            'class'=>Category::class,
            'choice_label'=>'title'
            
        ])
        ->add('Enregistrer',SubmitType::class)
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubCategory::class,
        ]);
    }
}
