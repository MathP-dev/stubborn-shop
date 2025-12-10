<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

class CartServiceTest extends KernelTestCase
{
    private CartService $cartService;
    private ProductRepository $productRepository;
    private SessionInterface $session;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->session = new Session(new MockArraySessionStorage());
        
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getSession')->willReturn($this->session);

        $this->productRepository = $this->createMock(ProductRepository::class);
        
        $this->cartService = new CartService($requestStack, $this->productRepository);
    }

    public function testAddProductToCart(): void
    {
        $this->cartService->add(1, 'M');
        
        $this->assertEquals(1, $this->cartService->getItemCount());
    }

    public function testAddSameProductIncreasesQuantity(): void
    {
        $this->cartService->add(1, 'M');
        $this->cartService->add(1, 'M');
        
        $this->assertEquals(2, $this->cartService->getItemCount());
    }

    public function testRemoveProductFromCart(): void
    {
        $this->cartService->add(1, 'M');
        $this->cartService->remove(1, 'M');
        
        $this->assertTrue($this->cartService->isEmpty());
    }

    public function testCalculateTotal(): void
    {
        $product1 = new Product();
        $product1->setName('Test Product 1');
        $product1->setPrice('29.90');

        $product2 = new Product();
        $product2->setName('Test Product 2');
        $product2->setPrice('45.00');

        $this->productRepository
            ->method('find')
            ->willReturnMap([
                [1, null, null, $product1],
                [2, null, null, $product2],
            ]);

        $this->cartService->add(1, 'M');
        $this->cartService->add(2, 'L');

        $total = $this->cartService->getTotal();
        
        $this->assertEquals(74.90, $total);
    }

    public function testClearCart(): void
    {
        $this->cartService->add(1, 'M');
        $this->cartService->add(2, 'L');
        
        $this->assertEquals(2, $this->cartService->getItemCount());
        $this->cartService->clear();
        $this->assertTrue($this->cartService->isEmpty());
    }
}
