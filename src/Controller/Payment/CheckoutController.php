<?php

namespace App\Controller\Payment;

use App\Service\CartService;
use App\Service\OrderService;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function __invoke(
        CartService $cartService,
        StripeService $stripeService,
        OrderService $orderService
    ): Response {
        if ($cartService->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_products');
        }

        $user = $this->getUser();
        $cartItems = $cartService->getCartWithDetails();

        $order = $orderService->createOrderFromCart($user, $cartItems);

        try {
            $session = $stripeService->createCheckoutSession($cartItems, $user);

            $order->setStripeSessionId($session->id);
            $orderService->finalizeOrder($order);

            return $this->redirect($session->url);

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création de la session de paiement : ' . $e->getMessage());
            return $this->redirectToRoute('app_cart');
        }
    }
}
