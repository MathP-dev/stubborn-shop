<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private const CART_SESSION_KEY = 'cart';

    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository
    ) {
    }

    /**
     * Ajoute un produit au panier
     */
    public function add(int $productId, string $size): void
    {
        $cart = $this->getCart();
        $key = $this->getCartKey($productId, $size);

        if (isset($cart[$key])) {
            $cart[$key]['quantity']++;
        } else {
            $cart[$key] = [
                'productId' => $productId,
                'size' => $size,
                'quantity' => 1,
            ];
        }

        $this->saveCart($cart);
    }

    /**
     * Retire un produit du panier
     */
    public function remove(int $productId, string $size): void
    {
        $cart = $this->getCart();
        $key = $this->getCartKey($productId, $size);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            $this->saveCart($cart);
        }
    }

    /**
     * Met à jour la quantité d'un produit
     */
    public function updateQuantity(int $productId, string $size, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($productId, $size);
            return;
        }

        $cart = $this->getCart();
        $key = $this->getCartKey($productId, $size);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $quantity;
            $this->saveCart($cart);
        }
    }

    /**
     * Récupère le panier avec les détails des produits
     */
    public function getCartWithDetails(): array
    {
        $cart = $this->getCart();
        $cartWithDetails = [];

        foreach ($cart as $key => $item) {
            $product = $this->productRepository->find($item['productId']);
            
            if ($product) {
                $cartWithDetails[$key] = [
                    'product' => $product,
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'subtotal' => (float)$product->getPrice() * $item['quantity'],
                ];
            }
        }

        return $cartWithDetails;
    }

    /**
     * Calcule le total du panier
     */
    public function getTotal(): float
    {
        $total = 0;
        $cartWithDetails = $this->getCartWithDetails();

        foreach ($cartWithDetails as $item) {
            $total += $item['subtotal'];
        }

        return $total;
    }

    /**
     * Compte le nombre d'articles dans le panier
     */
    public function getItemCount(): int
    {
        $cart = $this->getCart();
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    /**
     * Vide le panier
     */
    public function clear(): void
    {
        $this->saveCart([]);
    }

    /**
     * Vérifie si le panier est vide
     */
    public function isEmpty(): bool
    {
        return empty($this->getCart());
    }

    /**
     * Récupère le panier brut de la session
     */
    private function getCart(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get(self::CART_SESSION_KEY, []);
    }

    /**
     * Sauvegarde le panier en session
     */
    private function saveCart(array $cart): void
    {
        $session = $this->requestStack->getSession();
        $session->set(self::CART_SESSION_KEY, $cart);
    }

    /**
     * Génère une clé unique pour un produit + taille
     */
    private function getCartKey(int $productId, string $size): string
    {
        return sprintf('%d_%s', $productId, $size);
    }

    /**
     * Récupère le panier brut (pour traitement de commande)
     */
    public function getRawCart(): array
    {
        return $this->getCart();
    }
}