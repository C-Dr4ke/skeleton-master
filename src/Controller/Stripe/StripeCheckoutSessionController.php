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

   
    #[Route('/prepare', name: 'order_prepare')]
    public function prepareAction()
    {
        return $this->render('security/prepare.html.twig');
    }


    #[Route('/checkout', name: 'order_checkout')]
    public function checkout(PanierService $panier ): Response
    {
        
        $YOUR_DOMAIN = "http://127.0.0.1:8001";
        $user = $this->getUser();
        if (!$panier) {
            return $this->redirectToRoute('home');
        }
        Stripe::setApiKey('sk_test_51KicyHBoao3FmrU3GlRKLGlGCcArRJMXLNljmJfBtxPyiQ44YVETniqkCOSgC9uvL4RYvpkWD95B2iEmOqsYCAhT00I79Yv4Or');
        // dd($panier->getTotal());
        $session = Session::create([
            
            'line_items' => [[
                
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $user->getUserIdentifier(),
                    ],
                    'unit_amount' => $panier->getTotal()*100,
                ],
                'quantity' => 1,
            ]],
            "mode" => 'payment',
            'success_url' => $this->generateURL('success_url',[],
        UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateURL('cancel_url',[],
            UrlGeneratorInterface::ABSOLUTE_URL),
        ]);



        return $this->redirect($session->url, 303);
    }

    
}
