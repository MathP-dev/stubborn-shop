<?php

namespace App\Controller\Cart;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartShowController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function __invoke(CartService $cartService): Response
    {
        $cartItems = $cartService->getCartWithDetails();
        $total = $cartService->getTotal();

        return $this->render('cart/show.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }
}