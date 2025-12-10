<?php

namespace App\Controller\Product;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductShowController extends AbstractController
{
    #[Route('/product/{id}', name: 'app_product_show', requirements: ['id' => '\d+'])]
    public function __invoke(
        Product $product,
        Request $request,
        CartService $cartService
    ): Response {
        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $size = $form->get('size')->getData();

            $stock = $product->getStockForSize($size);

            if (! $stock || ! $stock->isAvailable()) {
                $this->addFlash('error', 'Ce produit n\'est plus disponible dans cette taille.');
                return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
            }

            $cartService->add($product->getId(), $size);
            $this->addFlash('success', 'Produit ajouté au panier !');

            return $this->redirectToRoute('app_cart');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
}
