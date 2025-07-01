<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\FoodGroup;
use App\Form\RecipeForm;
use App\Repository\UserRepository;
use App\Repository\RecipeRepository;
use App\Repository\IngredientRepository;
use App\Repository\FoodGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DashboardController extends AbstractDashboardController
{
    private UserRepository $userRepo;
    private RecipeRepository $recipeRepo;
    private IngredientRepository $ingredientRepo;
    private FoodGroupRepository $groupRepo;
    private EntityManagerInterface $em;

    public function __construct(
        UserRepository $userRepo,
        RecipeRepository $recipeRepo,
        IngredientRepository $ingredientRepo,
        FoodGroupRepository $groupRepo,
        EntityManagerInterface $em
    ) {
        $this->userRepo = $userRepo;
        $this->recipeRepo = $recipeRepo;
        $this->ingredientRepo = $ingredientRepo;
        $this->groupRepo = $groupRepo;
        $this->em = $em;
    }

    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(Request $request): Response
    {

    $userCount = $this->userRepo->count([]);
    $recipeCount = $this->recipeRepo->count([]);
    $ingredientCount = $this->ingredientRepo->count([]);
    $groupCount = $this->groupRepo->count([]);

    $conn = $this->em->getConnection();
    $sql = "
        SELECT MONTH(created_at) AS month, COUNT(*) AS count
        FROM recipe
        WHERE YEAR(created_at) = YEAR(CURDATE())
        GROUP BY month
    ";
    $stmt = $conn->prepare($sql);
    $results = $stmt->executeQuery()->fetchAllAssociative();

    $recipesPerMonth = array_fill(1, 12, 0);
    foreach ($results as $row) {
        $recipesPerMonth[(int)$row['month']] = (int)$row['count'];
    }

    $recipe = new Recipe();
    $form = $this->createForm(RecipeForm::class, $recipe);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->em->persist($recipe);
        $this->em->flush();
        $this->addFlash('success', 'Recette ajoutée avec succès');
        return $this->redirectToRoute('admin_dashboard');
    }

    return $this->render('admin/custom_dashboard.html.twig', [
        'userCount' => $userCount,
        'recipeCount' => $recipeCount,
        'ingredientCount' => $ingredientCount,
        'groupCount' => $groupCount,
        'recipesPerMonth' => $recipesPerMonth,
        'form' => $form->createView(),
    ]);
}


    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('/styles/admin.css')
            ->addCssFile('https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css')
            ->addJsFile('https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js')
            ->addJsFile('https://cdn.jsdelivr.net/npm/chart.js');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Backend Blog BB');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Recettes', 'fas fa-utensils', Recipe::class);
        yield MenuItem::linkToCrud('Ingrédients', 'fas fa-carrot', Ingredient::class);
        yield MenuItem::linkToCrud('Groupes alimentaires', 'fas fa-lemon', FoodGroup::class);
    }
}
