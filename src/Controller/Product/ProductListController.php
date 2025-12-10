<?php

namespace App\Controller\Product;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductListController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function __invoke(Request $request, ProductRepository $productRepository): Response
    {
        $priceRange = $request->query->get('price_range');
        
        $products = match($priceRange) {
            '10-29' => $productRepository->findByPriceRange(10, 29),
            '29-35' => $productRepository->findByPriceRange(29, 35),
            '35-50' => $productRepository->findByPriceRange(35, 50),
            default => $productRepository->findAll(),
        };

        return $this->render('product/list.html.twig', [
            'products' => $products,
            'currentPriceRange' => $priceRange,
        ]);
    }
}