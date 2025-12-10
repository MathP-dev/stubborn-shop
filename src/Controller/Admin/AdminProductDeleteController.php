<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminProductDeleteController extends AbstractController
{
    #[Route('/admin/product/{id}/delete', name: 'app_admin_product_delete', methods: ['POST'])]
    public function __invoke(
        Product $product,
        EntityManagerInterface $entityManager
    ): Response {
        // Supprimer l'image si elle existe
        if ($product->getImage()) {
            $imagePath = $this->getParameter('kernel.project_dir').'/public/upload/products/'.$product->getImage();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès !');

        return $this->redirectToRoute('app_admin_dashboard');
    }
}
