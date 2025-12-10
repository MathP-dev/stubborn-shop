<?php

namespace App\Controller\Auth;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\Request;

class VerifyEmailController extends AbstractController
{
    #[Route('/verify/{token}', name: 'app_verify_email')]
    public function __invoke(
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator,
        Request $request
    ): Response {
        $user = $userRepository->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Token de vérification invalide.');
            return $this->redirectToRoute('app_home');
        }

        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été vérifié avec succès !  Vous êtes maintenant connecté.');

        // Connecter automatiquement l'utilisateur
        return $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );
    }
}