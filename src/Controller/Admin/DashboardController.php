<?php

// src/Controller/Admin/DashboardController.php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\FoodGroup;
use App\Repository\UserRepository;
use App\Repository\RecipeRepository;
use App\Repository\IngredientRepository;
use App\Repository\FoodGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    // Déclarations des dépendances nécessaires
    private UserRepository $userRepo;
    private RecipeRepository $recipeRepo;
    private IngredientRepository $ingredientRepo;
    private FoodGroupRepository $groupRepo;
    private EntityManagerInterface $em;

    // Injection de dépendances via le constructeur
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

    // Point d'entrée du tableau de bord
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Récupérer les statistiques de base
        $userCount = $this->userRepo->count([]);
        $recipeCount = $this->recipeRepo->count([]);
        $ingredientCount = $this->ingredientRepo->count([]);
        $groupCount = $this->groupRepo->count([]);

        // Requête SQL brute pour les recettes par mois
        $conn = $this->em->getConnection();
        $sql = "
            SELECT MONTH(created_at) AS month, COUNT(*) AS count
            FROM recipe
            WHERE YEAR(created_at) = YEAR(CURDATE())
            GROUP BY month
        ";
        $stmt = $conn->prepare($sql);
        $results = $stmt->executeQuery()->fetchAllAssociative();

        // Préparation d’un tableau avec 12 mois initialisés à 0
        $recipesPerMonth = array_fill(1, 12, 0);

        // Injection des données récupérées dans le tableau
        foreach ($results as $row) {
            $recipesPerMonth[(int)$row['month']] = (int)$row['count'];
        }

        // Rendu de la vue personnalisée
        return $this->render('admin/custom_dashboard.html.twig', [
            'userCount' => $userCount,
            'recipeCount' => $recipeCount,
            'ingredientCount' => $ingredientCount,
            'groupCount' => $groupCount,
            'recipesPerMonth' => $recipesPerMonth
        ]);
    }

    // Ajout de fichiers CSS/JS spécifiques pour le style et les graphiques
    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('/styles/admin.css') // Ton thème personnalisé
            ->addCssFile('https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css') // Flowbite
            ->addJsFile('https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js')  // Flowbite
            ->addJsFile('https://cdn.jsdelivr.net/npm/chart.js'); // Chart.js pour les stats
    }

    // Configuration du titre du dashboard
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Backend Blog BB');
    }

    // Configuration du menu latéral (liens vers entités)
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Recettes', 'fas fa-utensils', Recipe::class);
        yield MenuItem::linkToCrud('Ingrédients', 'fas fa-carrot', Ingredient::class);
        yield MenuItem::linkToCrud('Groupes alimentaires', 'fas fa-lemon', FoodGroup::class);
    }
}
