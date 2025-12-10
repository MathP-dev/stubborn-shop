<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    /**
     * Envoie l'email de vérification
     */
    public function sendVerificationEmail(User $user): void
    {
        $verificationUrl = $this->urlGenerator->generate(
            'app_verify_email',
            ['token' => $user->getVerificationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new TemplatedEmail())
            ->from(new Address('stubborn@blabla.com', 'Stubborn'))
            ->to($user->getEmail())
            ->subject('Confirmez votre inscription sur Stubborn')
            ->htmlTemplate('emails/verification. html.twig')
            ->context([
                'user' => $user,
                'verificationUrl' => $verificationUrl,
            ]);

        $this->mailer->send($email);
    }

    /**
     * Envoie la confirmation de commande
     */
    public function sendOrderConfirmation(User $user, string $orderNumber, float $total): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('stubborn@blabla.com', 'Stubborn'))
            ->to($user->getEmail())
            ->subject('Confirmation de votre commande #' . $orderNumber)
            ->htmlTemplate('emails/order_confirmation.html.twig')
            ->context([
                'user' => $user,
                'orderNumber' => $orderNumber,
                'total' => $total,
            ]);

        $this->mailer->send($email);
    }
}