<?php

namespace App\Controller\Payment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentCancelController extends AbstractController
{
    #[Route('/payment/cancel', name: 'app_payment_cancel')]
    public function __invoke(): Response
    {
        $this->addFlash('warning', 'Paiement annulé.  Votre panier a été conservé.');
        
        return $this->redirectToRoute('app_cart');
    }
}