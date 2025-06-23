<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class IngredientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $recipes = $manager->getRepository(Recipe::class)->findAll();

        if (count($recipes) === 0) {
            throw new \Exception('Aucune recette trouvée. Assure-toi d\'avoir exécuté les RecipeFixtures avant.');
        }

        $units = ['g', 'ml', 'cuil. à soupe', 'cuil. à café', 'tranche(s)', 'pièce(s)', 'verre(s)'];

        foreach ($recipes as $recipe) {
            $ingredientCount = random_int(3, 6);

            for ($i = 0; $i < $ingredientCount; $i++) {
                $ingredient = new Ingredient();
                $ingredient->setName($faker->word());
                $ingredient->setQuantity((string) $faker->randomFloat(1, 1, 300)); // ex: "25.5"
                $ingredient->setUnit($faker->randomElement($units));
                $ingredient->setRecipe($recipe);

                $manager->persist($ingredient);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RecipeFixtures::class,
        ];
    }
}
