<?php

namespace App\Controller\Admin;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function __invoke(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'products' => $products,
        ]);
    }
}
