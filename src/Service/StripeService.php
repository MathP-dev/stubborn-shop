<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\User;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class StripeService
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    }

    /**
     * Crée une session de paiement Stripe
     */
    public function createCheckoutSession(array $cartItems, User $user): Session
    {
        $lineItems = [];

        foreach ($cartItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['product']->getName() . ' - ' . $item['size'],
                        'images' => [$this->getProductImageUrl($item['product']->getImage())],
                    ],
                    'unit_amount' => (int)($item['product']->getPrice() * 100), // En centimes
                ],
                'quantity' => $item['quantity'],
            ];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate('app_payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->urlGenerator->generate('app_payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'customer_email' => $user->getEmail(),
            'metadata' => [
                'user_id' => $user->getId(),
            ],
        ]);

        return $session;
    }

    /**
     * Récupère une session Stripe par son ID
     */
    public function retrieveSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }

    /**
     * Génère l'URL complète de l'image du produit
     */
    private function getProductImageUrl(?string $imageName): string
    {
        if (!$imageName) {
            return $this->urlGenerator->generate('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL) . 'images/logo.png';
        }

        return $this->urlGenerator->generate('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL) . 'upload/products/' . $imageName;
    }
}
