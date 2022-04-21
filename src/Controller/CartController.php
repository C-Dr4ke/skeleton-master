<?php

namespace App\Controller;

use App\Service\Panier\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function cart(PanierService $panierService)
    {
       
      
        $affiche = true;
        
     $panierWithData = $panierService->getFullCart();
   
    $total =0;
     $total = $panierService->getTotal();
   
    
    //  dd($panierWithData);
     return $this->render('cart/cart.html.twig',[ 
        'items'=>$panierWithData,
        'affiche' => $affiche,
        'total'=>$total
     ]);
    }

    #[Route('/addCart/{id}/{param}' , name: 'addCart')]
     public function addCart($id, PanierService $panierService,$param)
     {
         
       
        $entry=null;
      if($param!=='cart'):

      $id= $panierService->add($id, $param,$entry );

     else:
       //dd('coucou');
        $panierService->add($id, $param,$entry );
        return $this->redirectToRoute('cart');
     endif;


    
   
      if($param == 'boisson'  ) {
        
        return $this->redirectToRoute('choixBoisson', ['id'=>$id]);
    }
    else if($param == 'carte' ) {
        return $this->redirectToRoute('carte');
    }
     }
 
     #[Route('/deleteCart/{id}', name: 'deleteCart')]
     public function deleteCart($id, PanierService $panierService)
     {
        $panierService->delete($id);
        
    return $this->redirectToRoute('cart');
     }

    
    #[Route('/removeCart/{id}', name: 'removeCart')]
    public function removeCart(PanierService $panierService, $id)
    {
        $panierService->removeQuantity($id);

        return $this->redirectToRoute('cart');
    }


     
    #[Route('/destroyCart', name: 'destroyCart')]
    public function destroyCart(PanierService $panierService)
    {
        //$request->cookies->set('panierDestroy',$panierService->fullCart());

        $panierService->destroy();

        return $this->redirectToRoute('home');
    }
}
