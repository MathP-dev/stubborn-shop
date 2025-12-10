<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Stock;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[IsGranted('ROLE_ADMIN')]
class AdminProductCreateController extends AbstractController
{
    #[Route('/admin/product/create', name: 'app_admin_product_create')]
    public function __invoke(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename. '-'. uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir'). '/public/upload/products',
                        $newFilename
                    );
                    $product->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            // Créer les stocks pour chaque taille
            foreach (Stock::SIZES as $size) {
                $quantity = $form->get('stock_' . $size)->getData() ??  0;

                $stock = new Stock();
                $stock->setProduct($product);
                $stock->setSize($size);
                $stock->setQuantity($quantity);

                $product->addStock($stock);
                $entityManager->persist($stock);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit créé avec succès ! ');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/product_create.html.twig', [
            'form' => $form,
        ]);
    }
}
