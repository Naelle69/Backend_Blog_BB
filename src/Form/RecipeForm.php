<?php

namespace App\Form;

use App\Entity\FoodGroup;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class RecipeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Image de la recette',
                'mapped' => true, // true car Vich lie bien l’objet
            ])
            ->add('updatedAt')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('time')
            ->add('foodGroup', EntityType::class, [
                'class' => FoodGroup::class,
                'choice_label' => 'id',
            ])
            ->add('ingredients', EntityType::class, [
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Les ingrédients',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
