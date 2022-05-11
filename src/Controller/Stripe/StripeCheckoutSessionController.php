<?php

namespace App\Controller\Stripe;

use App\Service\Panier\PanierService;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeCheckoutSessionController extends AbstractController
{
    #[Route('/checkout', name: 'order_checkout')]
    public function checkout(PanierService $panier ): Response
    {
    // Si le panier est vide alors on est renvoyer vers l'acceuil
    if (!$panier) {
        return $this->redirectToRoute('home');
    }
    // Création du tableau de données qui va être renvoyer vers l'API Stripe
    $products_for_stripe = [];

    // On entre le contenu du panier dans le tableau de données qui sera renvoyer vers Stripe(Produit, quantité, prix)
    foreach ($panier->getFullCart() as $product){
        $products_for_stripe[]=[
            'price_data'=>[
                'currency'=>'eur',
                'unit_amount'=> $product['product']->getPrice()*100,
                'product_data'=>[
                    'name' => $product['product']->getTitle(),
                    'images'=> [ "http://127.0.0.1:8001/public/upload/".$product['product']->getPicture()],
                ],
            ],
            'quantity' => $product['quantity'],
        ];
    }
    // Clé de paiment test de l'API Stripe
    Stripe::setApiKey('sk_test_51KicyHBoao3FmrU3GlRKLGlGCcArRJMXLNljmJfBtxPyiQ44YVETniqkCOSgC9uvL4RYvpkWD95B2iEmOqsYCAhT00I79Yv4Or');

    // Configuration des données qui seront renvoyées vers stripe
    $session = Session::create([
        'customer_email'=> $this->getUser()->getEmail(),
        'line_items' => [ $products_for_stripe],
        "mode" => 'payment',
        'success_url' => $this->generateURL('success_url',[],
        UrlGeneratorInterface::ABSOLUTE_URL),
        'cancel_url' => $this->generateURL('cancel_url',[],
        UrlGeneratorInterface::ABSOLUTE_URL),
    ]);
    // Redirection vers l'API Stripe
    return $this->redirect($session->url, 303);
    }

    
}
