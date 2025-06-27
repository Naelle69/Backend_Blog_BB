<?php

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class IngredientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ingredient::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('name', 'Nom'),

            NumberField::new('quantity', 'Quantité'),

            TextField::new('unit', 'Unité'),

            AssociationField::new('recipes', 'Recettes')
                ->setCrudController(RecipeCrudController::class),
        ];
    }
}
