<?php

namespace App\Controller\Admin;

use App\Entity\FoodGroup;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FoodGroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FoodGroup::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom du groupe alimentaire'),
        ];
    }
}
