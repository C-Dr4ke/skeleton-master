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

        // Récupération du panier complet 
        $panierWithData = $panierService->getFullCart();
        
        // Initialisation du total à 0
        $total =0;
        $total = $panierService->getTotal();

        return $this->render('cart/cart.html.twig',[ 
            'items'=>$panierWithData,
            'total'=>$total
        ]);

    }

    #[Route('/addCart/{id}/{param}' , name: 'addCart')]
    public function addCart($id, PanierService $panierService,$param)
    {
         
        $entry=null;
        //Si on ajouter un produit mais que l'on est pas dans le panier alors on augmente la quantité de ce produit
        if($param!=='cart'){
            $id= $panierService->add($id, $param,$entry );
        }
        //Si on est dans le panier alors on augmente la quantité de ce produit
        else{
            $panierService->add($id, $param,$entry );
            return $this->redirectToRoute('cart');
        }
        // Si on Ajoute un menu avec boisson alors on est rediriger vers la page boisson
        if($param == 'boisson'  ) {
            return $this->redirectToRoute('choixBoisson', ['id'=>$id]);
        }
        // Si on Ajoute un produit sans boisson incluse
        else if($param == 'carte' ) {
            return $this->redirectToRoute('carte');
        }
    }
 
     #[Route('/deleteCart/{id}', name: 'deleteCart')]
     public function deleteCart($id, PanierService $panierService)
     {  
        // On appele la fonction qui vide le panier
        $panierService->delete($id);  
        return $this->redirectToRoute('cart');
     }

    
    #[Route('/removeCart/{id}', name: 'removeCart')]
    public function removeCart(PanierService $panierService, $id)
    {   
        // On appele la fonction qui baisse la quantité de 1
        $panierService->removeQuantity($id);
        return $this->redirectToRoute('cart');
    }


     
    #[Route('/destroyCart', name: 'destroyCart')]
    public function destroyCart(PanierService $panierService)
    {
        
        // On appele la fonction supprime le panier
        $panierService->destroy();

        return $this->redirectToRoute('home');
    }
}
