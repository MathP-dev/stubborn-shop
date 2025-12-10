<?php

namespace App\Controller\Payment;

use App\Service\CartService;
use App\Service\EmailService;
use App\Service\OrderService;
use App\Service\StripeService;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentSuccessController extends AbstractController
{
    #[Route('/payment/success', name: 'app_payment_success')]
    public function __invoke(
        Request $request,
        StripeService $stripeService,
        CartService $cartService,
        OrderRepository $orderRepository,
        OrderService $orderService,
        EmailService $emailService
    ): Response {
        $sessionId = $request->query->get('session_id');

        if (!$sessionId) {
            $this->addFlash('error', 'Session invalide.');
            return $this->redirectToRoute('app_home');
        }

        try {
            $session = $stripeService->retrieveSession($sessionId);

            $order = $orderRepository->findOneBy(['stripeSessionId' => $sessionId]);

            if (!$order) {
                $this->addFlash('error', 'Commande introuvable.');
                return $this->redirectToRoute('app_home');
            }

            $orderService->finalizeOrder($order);

            $cartService->clear();

            try {
                $emailService->sendOrderConfirmation(
                    $order->getUser(),
                    (string)$order->getId(),
                    (float)$order->getTotalPrice()
                );
            } catch (\Exception $e) {
                // Email non bloquant
            }

            $this->addFlash('success', 'Paiement réussi ! Merci pour votre commande.');

            return $this->render('payment/success.html.twig', [
                'order' => $order,
            ]);

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la vérification du paiement.');
            return $this->redirectToRoute('app_home');
        }
    }
}
