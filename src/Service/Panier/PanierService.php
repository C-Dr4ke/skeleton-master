<?php

namespace App\Service\Panier;



use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\Length;

class PanierService
{
    protected $session;
    protected $productRepository;


    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }



    public function add(int $id, $param, $entry, $cart = null)
    {
        // $this->session->remove('panier');
        // die;
        $panier = $this->session->get('panier', []);
        //dd($panier);

        $index = -1;

        if ($param == 'carte') {



            $ajout = -1;
            for ($i = 0; $i < count($panier); $i++) {
                if ($panier[$i]['id'] == $id && $param == 'carte' && !$entry) {
                    $ajout = $i;
                };
            };

            if ($ajout == -1) {
                $panier[] = ['id' => $id, 'quantite' => 1];
                $index = count($panier) - 1;
            } else {
                $panier[$ajout]['quantite']++;
                $index = $ajout;
            };
        } else {
            //  dd($cart);

            if ($param !== 'cart') :



                if (!$entry && $entry !== '0') {

                    $panier[] = ['id' => $id, 'quantite' => 1, 'boisson' => 0];
                    $index = count($panier) - 1;
                    //  dd($entry, $id, $panier, $index);  

                } else {
                    $double = false;
                    for ($i = 0; $i < count($panier); $i++) {
                        // dd($item['id']==$panier[$entry]['id'] && $item['boisson']==$id);
                        if ($panier[$i]['id'] == $panier[$entry]['id'] && $panier[$i]['boisson'] == $id) {

                            $panier[$i]['quantite']++;
                            //dd($item['quantite']);

                            $double = true;
                        }
                    }

                    if ($double == false) {

                        $panier[$entry]['boisson'] = $id;
                        $index = $entry;
                    } else {
                        array_pop($panier);
                    };
                }




            else :
                // dd($panier, $id,$panier[$id]['quantite'] );
                $panier[$id - 1]['quantite']++;



            endif;
        }

        $this->session->set('panier', $panier);
        return $index;
    }

    public function delete(int $id)
    {

        $panier = $this->session->get('panier', []);
        
        //dd($id);
        if (!empty($panier[$id-1])) {
  
            unset($panier[$id-1]);
            $panier = array_merge($panier);
        }
        $this->session->set('panier', $panier);
    }

    public function getFullCart(): array
    {


        // Recuperation de l'id du produit et de la quantité
        $panier = $this->session->get('panier', []);
        $panierWithData = [];

        //dd($panier);


        // On récupère dans la boucle les informations du produit que l'on a ajouté au panier
        //array_unique($panier);
        foreach ($panier as $index => $detail) {



            if (isset($detail['boisson'])) :

                $panierWithData[] = [
                    'product' => $this->productRepository->find($detail['id']),
                    'quantity' => $detail['quantite'],
                    'drinks' => $this->productRepository->find($detail['boisson'])
                ];
            else :
                
        // $this->session->remove('panier');
        // die;
                $panierWithData[] = [
                    'product' => $this->productRepository->find($detail['id']),
                    'quantity' => $detail['quantite']
                ];

            endif;
        }
        // dd($panierWithData);
        return $panierWithData;
    }

    public function getTotal()
    {

        $total = 0;

        foreach ($this->getFullCart() as $item) {
            // dd($item['quantity'], $item['product']->getPrice());
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
        //  dd($total);
        return $total;
    }



    public function removeQuantity(int $id)
    {
        
        $panier = $this->session->get('panier', []);
   

        // dd($panier[0]);
        if ($panier[$id-1]['quantite'] == 1) :
            
            // dd($panier);
            unset($panier[$id-1]);
            $panier = array_merge($panier);
        else :
            // dd($panier);
            $panier[$id-1]['quantite']--;
        endif;

        $this->session->set('panier', $panier);
    }

    public function destroy()
    {
        $this->session->remove('panier');
    }
}
