<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private StockRepository $stockRepository
    ) {
    }

    /**
     * Crée une commande à partir du panier
     */
    public function createOrderFromCart(User $user, array $cartItems, ? string $stripeSessionId = null): Order
    {
        $order = new Order();
        $order->setUser($user);
        $order->setStatus(Order::STATUS_PENDING);
        $order->setStripeSessionId($stripeSessionId);

        $total = 0;

        foreach ($cartItems as $item) {
            $product = $item['product'];
            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setSize($item['size']);
            $orderItem->setQuantity($item['quantity']);
            $orderItem->setUnitPrice($product->getPrice());

            $order->addOrderItem($orderItem);
            $total += $orderItem->getTotal();

            $this->entityManager->persist($orderItem);
        }

        $order->setTotalPrice((string)$total);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    /**
     * Finalise une commande après paiement réussi
     */
    public function finalizeOrder(Order $order): void
    {
        $order->setStatus(Order::STATUS_PAID);

        // Décrémenter le stock
        foreach ($order->getOrderItems() as $orderItem) {
            $stock = $orderItem->getProduct()->getStockForSize($orderItem->getSize());
            if ($stock) {
                $stock->decreaseQuantity($orderItem->getQuantity());
                $this->entityManager->persist($stock);
            }
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    /**
     * Annule une commande
     */
    public function cancelOrder(Order $order): void
    {
        $order->setStatus(Order::STATUS_CANCELLED);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}