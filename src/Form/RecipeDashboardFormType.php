<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\FoodGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeDashboardFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la recette',
                'attr' => ['class' => 'block w-full rounded-lg border-gray-300 shadow-sm']
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'block w-full rounded-lg border-gray-300 shadow-sm']
            ])
            ->add('time', IntegerType::class, [
                'label' => 'Temps (minutes)',
                'attr' => ['class' => 'block w-full rounded-lg border-gray-300 shadow-sm']
            ])
            ->add('foodGroup', EntityType::class, [
                'class' => FoodGroup::class,
                'choice_label' => 'name',
                'label' => 'Groupe alimentaire',
                'attr' => ['class' => 'block w-full rounded-lg border-gray-300 shadow-sm']
            ])
            ->add('ingredients', EntityType::class, [
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // Affiche des cases à cocher
                'label' => 'Choisissez les ingrédients',
                'attr' => ['class' => 'grid grid-cols-2 gap-2']
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
