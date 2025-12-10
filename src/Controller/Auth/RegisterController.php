<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function __invoke(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        EmailService $emailService
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            // Génération du token de vérification
            $user->setVerificationToken(bin2hex(random_bytes(32)));
            $user->setIsVerified(false);

            $entityManager->persist($user);
            $entityManager->flush();

            // Envoi de l'email de vérification
            try {
                $emailService->sendVerificationEmail($user);
                $this->addFlash('success', 'Inscription réussie !  Veuillez vérifier votre email pour activer votre compte.');
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Compte créé mais l\'email de vérification n\'a pas pu être envoyé.');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form,
        ]);
    }
}
