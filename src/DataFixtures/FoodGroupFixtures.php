<?php

namespace App\DataFixtures;

use App\Entity\FoodGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FoodGroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $groups = [
            'Viandes',
            'Fruits & Légumes',
            'Féculents',
            'Produits laitiers',
            'Poissons',
            'Céréales & graines',
            'Boissons pour bébé',
            'Desserts',
            'Repas mixés / purées',
            'Snacks / goûters sains',
        ];

        foreach ($groups as $group) {
            $foodGroup = new FoodGroup();
            $foodGroup->setName($group);
            $manager->persist($foodGroup);
        }

        $manager->flush();
    }
}
