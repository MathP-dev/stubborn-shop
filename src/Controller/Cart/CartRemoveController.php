<?php

namespace App\Controller\Cart;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartRemoveController extends AbstractController
{
    #[Route('/cart/remove/{productId}/{size}', name: 'app_cart_remove', methods: ['POST'])]
    public function __invoke(
        int $productId,
        string $size,
        CartService $cartService
    ): Response {
        $cartService->remove($productId, $size);
        $this->addFlash('success', 'Article retiré du panier.');

        return $this->redirectToRoute('app_cart');
    }
}