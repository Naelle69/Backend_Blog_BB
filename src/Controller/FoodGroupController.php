<?php

namespace App\Controller;

use App\Entity\FoodGroup;
use App\Form\FoodGroupForm;
use App\Repository\FoodGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/food/group')]
final class FoodGroupController extends AbstractController
{
    #[Route(name: 'app_food_group_index', methods: ['GET'])]
    public function index(FoodGroupRepository $foodGroupRepository): Response
    {
        return $this->render('food_group/index.html.twig', [
            'food_groups' => $foodGroupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_food_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $foodGroup = new FoodGroup();
        $form = $this->createForm(FoodGroupForm::class, $foodGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($foodGroup);
            $entityManager->flush();

            return $this->redirectToRoute('app_food_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('food_group/new.html.twig', [
            'food_group' => $foodGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_food_group_show', methods: ['GET'])]
    public function show(FoodGroup $foodGroup): Response
    {
        return $this->render('food_group/show.html.twig', [
            'food_group' => $foodGroup,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_food_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FoodGroup $foodGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FoodGroupForm::class, $foodGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_food_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('food_group/edit.html.twig', [
            'food_group' => $foodGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_food_group_delete', methods: ['POST'])]
    public function delete(Request $request, FoodGroup $foodGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$foodGroup->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($foodGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_food_group_index', [], Response::HTTP_SEE_OTHER);
    }
}
