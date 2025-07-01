<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationApiController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
        // On décode le JSON reçu depuis le front
        $data = json_decode($request->getContent(), true);

        // On vérifie que les champs attendus existent
        if (!isset($data['email'], $data['plainPassword'])) {
            return $this->json(['message' => 'Données incomplètes.'], 400);
        }

        $email = $data['email'];
        $plainPassword = $data['plainPassword'];

        // Vérification si l'utilisateur existe déjà
        if ($em->getRepository(User::class)->findOneBy(['email' => $email])) {
            return $this->json(['message' => 'Cet email est déjà utilisé.'], 409);
        }

        // Création de l'utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);

        // Hash du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Validation de l'entité
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['message' => 'Erreur de validation.', 'errors' => (string) $errors], 400);
        }

        // Persistance en BDD
        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'Utilisateur enregistré avec succès.'], 201);
    }
}
