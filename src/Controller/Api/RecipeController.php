<?php

namespace App\Controller\Api;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/* #[Route('/api', name: 'api_')] */
class RecipeController extends AbstractController
{
    #[Route('/recipes', name: 'recipes_list', methods: ['GET'])]
    public function list(RecipeRepository $recipeRepository): JsonResponse
    {
        $recipes = $recipeRepository->findAll();
        $latestRecipes = $recipeRepository->findLatest(3);


        $data = [];

        foreach ($recipes as $recipe) {
            $ingredients = [];
            foreach ($recipe->getIngredients() as $ingredient) {
                $ingredients[] = [
                    'id' => $ingredient->getId(),
                    'name' => $ingredient->getName(),
                    'unit' => $ingredient->getUnit(),
                    'quantity' => $ingredient->getQuantity(),
                ];
            }

            $data[] = [
                'id' => $recipe->getId(),
                'title' => $recipe->getTitle(),
                'content' => $recipe->getContent(),
                'time' => $recipe->getTime(),
                'image' => $recipe->getImage(),
                'foodGroup' => $recipe->getFoodGroup()?->getName(),
                'createdAt' => $recipe->getCreatedAt()?->format('Y-m-d H:i:s'),
                'updatedAt' => $recipe->getUpdatedAt()?->format('Y-m-d H:i:s'),
                'ingredients' => $ingredients,
            ];
        }

        return $this->json($data);
    }
}
