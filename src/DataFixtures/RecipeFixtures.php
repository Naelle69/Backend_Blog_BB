<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\FoodGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // On récupère tous les groupes d'aliments pour les associer aux recettes
        $foodGroups = $manager->getRepository(FoodGroup::class)->findAll();

        if (count($foodGroups) === 0) {
            throw new \Exception('Aucun groupe d\'aliments trouvé. Charge les FoodGroupFixtures d\'abord.');
        }

        for ($i = 0; $i < 20; $i++) {
            $recipe = new Recipe();

            $recipe->setTitle($faker->sentence(3));
            $recipe->setContent($faker->paragraphs(3, true));
            $recipe->setFoodGroup($faker->randomElement($foodGroups));
            $recipe->setTime($faker->numberBetween(5, 30)); // ⏱ Durée de préparation (5 à 30 min)
            $recipe->setUpdatedAt(new \DateTimeImmutable());

            // 📅 Date de création réaliste (convertie en immutable)
            $recipe->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-1 year', 'now')
                )
            );

            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FoodGroupFixtures::class,
        ];
    }
}
