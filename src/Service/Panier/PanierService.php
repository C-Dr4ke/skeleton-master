<?php

namespace App\Service\Panier;



use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{
    protected $session;
    protected $productRepository;

  
    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }
   


    public function add(int $id){

        $panier = $this->session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
  
        $panier[$id] = 1;
      }
      $this->session->set('panier',$panier);
    }

    public function delete(int $id) {

        $panier = $this->session->get('panier',[]);

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $this->session->set('panier',$panier);
    }

    public function getFullCart() :array {

            
    // Recuperation de l'id du produit et de la quantité
     $panier = $this->session->get('panier',[]);
     $panierWithData = [];
     
    
     // On récupère dans la boucle les informations du produit que l'on a ajouté au panier
     foreach ($panier as $id => $qunatity) {
     
         $panierWithData[] = [
             'product'=> $this->productRepository->find($id),
             'quantity'=>$qunatity
         ];
      
     }
     return $panierWithData;
    }

    public function getTotal() {

     $total = 0;  

     foreach($this->getFullCart() as $item){
         $totalItem = $item['product']->getPrice()*$item['quantity'];
         $total += $totalItem ;
     }
    //  dd($total);
     return $total;
    }

  

    public function removeQuantity(int $id)
    {

        $panier = $this->session->get('panier', []);

        // dd($panier);
        if ($panier[$id] == 1):
            unset($panier[$id]);
        else:
            $panier[$id]--;
        endif;

        $this->session->set('panier', $panier);

        
    }

    public function destroy()
    {
        $this->session->remove('panier');


    }
}