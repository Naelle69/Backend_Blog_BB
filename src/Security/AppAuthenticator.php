<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    // Permet de récupérer la dernière URL protégée avant login
    use TargetPathTrait;

    // Nom de la route de connexion
    public const LOGIN_ROUTE = 'app_login';

    // Injection du générateur d'URL
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    // Authentification de l'utilisateur avec les données du formulaire
    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email'); // Récupère l'email du formulaire

        // Sauvegarde l'email pour le préremplir en cas d'erreur
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        // Crée un "passeport" contenant l'utilisateur, le mot de passe et les badges de sécurité
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    // Méthode appelée après une authentification réussie
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Si l'utilisateur tentait d'accéder à une page protégée, on le redirige là
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Récupère l'utilisateur authentifié
        $user = $token->getUser();

        // Redirection spécifique si l'utilisateur est un administrateur
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('admin'));
        }

        // Redirection par défaut pour un utilisateur classique
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    // Retourne l'URL de la page de connexion (si besoin de rediriger vers login)
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
