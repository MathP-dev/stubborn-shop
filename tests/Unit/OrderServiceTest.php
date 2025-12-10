<?php

namespace App\Tests\Unit;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Stock;
use App\Entity\User;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderServiceTest extends KernelTestCase
{
    private ?OrderService $orderService = null;
    private ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->orderService = $container->get(OrderService::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->entityManager !== null) {
            $this->entityManager->close();
            $this->entityManager = null;
        }

        $this->orderService = null;
    }

    public function testCreateOrderFromCart(): void
    {
        $user = new User();
        $user->setEmail('test_' . uniqid() . '@example.com');
        $user->setName('Test User');
        $user->setPassword('hashed_password');
        $this->entityManager->persist($user);

        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice('29.90');
        $this->entityManager->persist($product);

        $this->entityManager->flush();

        $cartItems = [
            [
                'product' => $product,
                'size' => 'M',
                'quantity' => 2,
                'subtotal' => 59.80,
            ],
        ];

        $order = $this->orderService->createOrderFromCart($user, $cartItems, 'test_session_id');

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(Order::STATUS_PENDING, $order->getStatus());
        $this->assertEquals('test_session_id', $order->getStripeSessionId());
        $this->assertEquals('59.8', $order->getTotalPrice());
        $this->assertCount(1, $order->getOrderItems());
    }

    public function testFinalizeOrderChangesStatus(): void
    {
        $user = new User();
        $user->setEmail('test_' . uniqid() . '@example.com');
        $user->setName('Test User');
        $user->setPassword('hashed_password');
        $this->entityManager->persist($user);

        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice('29.90');
        $this->entityManager->persist($product);

        $stock = new Stock();
        $stock->setProduct($product);
        $stock->setSize('M');
        $stock->setQuantity(10);
        $this->entityManager->persist($stock);

        $this->entityManager->flush();

        $cartItems = [
            [
                'product' => $product,
                'size' => 'M',
                'quantity' => 2,
                'subtotal' => 59.80,
            ],
        ];

        $order = $this->orderService->createOrderFromCart($user, $cartItems);
        $this->orderService->finalizeOrder($order);

        $this->assertEquals(Order::STATUS_PAID, $order->getStatus());
    }

    public function testCancelOrder(): void
    {
        $user = new User();
        $user->setEmail('test_' . uniqid() . '@example.com');
        $user->setName('Test User');
        $user->setPassword('hashed_password');
        $this->entityManager->persist($user);

        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice('29.90');
        $this->entityManager->persist($product);

        $this->entityManager->flush();

        $cartItems = [
            [
                'product' => $product,
                'size' => 'M',
                'quantity' => 1,
                'subtotal' => 29.90,
            ],
        ];

        $order = $this->orderService->createOrderFromCart($user, $cartItems);
        $this->orderService->cancelOrder($order);

        $this->assertEquals(Order::STATUS_CANCELLED, $order->getStatus());
    }
}
