<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Stock;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[IsGranted('ROLE_ADMIN')]
class AdminProductEditController extends AbstractController
{
    #[Route('/admin/product/{id}/edit', name: 'app_admin_product_edit')]
    public function __invoke(
        Product $product,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel. project_dir').'/public/upload/products',
                        $newFilename
                    );

                    // Supprimer l'ancienne image si elle existe
                    if ($product->getImage()) {
                        $oldImagePath = $this->getParameter('kernel.project_dir').'/public/upload/products/'.$product->getImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $product->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            // Mettre à jour les stocks
            foreach (Stock::SIZES as $size) {
                $quantity = $form->get('stock_' .  $size)->getData();
                $stock = $product->getStockForSize($size);

                if ($stock) {
                    $stock->setQuantity($quantity);
                } else {
                    $stock = new Stock();
                    $stock->setProduct($product);
                    $stock->setSize($size);
                    $stock->setQuantity($quantity);
                    $product->addStock($stock);
                    $entityManager->persist($stock);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès !');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/product_edit. html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
}
