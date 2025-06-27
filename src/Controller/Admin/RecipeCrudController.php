<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Entity\FoodGroup;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

class RecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('title', 'Titre'),

            TextEditorField::new('content', 'Contenu'),

            // Champ pour uploader une image (VichUploader)
            Field::new('imageFile', 'Image')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),

            // Champ pour afficher l’image dans l’index
            ImageField::new('image')
                ->setBasePath('/uploads/images') // adapte selon ton répertoire public
                ->onlyOnIndex(),

            // Relation avec FoodGroup
            AssociationField::new('foodGroup', 'Groupe alimentaire')
                ->setCrudController(FoodGroupCrudController::class),

            AssociationField::new('ingredients', 'Les ingrédients')
                ->setFormTypeOptions([
                    'by_reference' => false,
                    'expanded' => true,
                    'multiple' => true,
                ]),
        ];
    }
}

