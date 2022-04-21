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
        
  
        if (!$panier) {
            return $this->redirectToRoute('home');
        }

        $products_for_stripe = [];

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

   
        Stripe::setApiKey('sk_test_51KicyHBoao3FmrU3GlRKLGlGCcArRJMXLNljmJfBtxPyiQ44YVETniqkCOSgC9uvL4RYvpkWD95B2iEmOqsYCAhT00I79Yv4Or');
        // dd($panier->getTotal());
        $session = Session::create([
           
            'line_items' => [ $products_for_stripe],
            "mode" => 'payment',
            'success_url' => $this->generateURL('success_url',[],
        UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateURL('cancel_url',[],
            UrlGeneratorInterface::ABSOLUTE_URL),
        ]);



        return $this->redirect($session->url, 303);
    }

    
}
