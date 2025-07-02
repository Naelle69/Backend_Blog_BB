<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    // Cette méthode gère l'inscription des utilisateurs via le formulaire /register
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,                               // Requête HTTP entrante (contient les données POST du formulaire)
        EntityManagerInterface $em,                     // Permet d'enregistrer les entités en base de données
        UserPasswordHasherInterface $passwordHasher     // Sert à hasher le mot de passe de manière sécurisée
    ): Response {
        $user = new User();                             // Création d'une nouvelle instance de l'entité User

        // Création du formulaire d'inscription lié à l'utilisateur
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);                 // Traitement des données envoyées (POST)

        // Si le formulaire est soumis et valide (validation Symfony OK)
        if ($form->isSubmitted() && $form->isValid()) {
            // Hashage sécurisé du mot de passe fourni
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);        // Affectation du mot de passe hashé
            $user->setRoles(['ROLE_USER']);             // Définition du rôle par défaut

            try {
                // Enregistrement de l'utilisateur en base de données
                $em->persist($user);
                $em->flush();

                // Message flash en cas de succès, redirection vers la page de connexion
                $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
                return $this->redirectToRoute('app_login');
            } catch (UniqueConstraintViolationException $e) {
                // Si l'email est déjà utilisé (clé unique), on redirige vers la connexion avec un message
                $this->addFlash('warning', 'Cet email est déjà utilisé. Veuillez vous connecter ou réinitialiser votre mot de passe.');
                return $this->redirectToRoute('app_login');
            }
        }

        // Si le formulaire n'est pas encore soumis ou contient des erreurs, on l'affiche à nouveau
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),  // On passe le formulaire à la vue Twig
        ]);
    }
}
