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

    public function add(int $id, $param, $entry, $cart = null)
    {
        // Récupération du panier
        $panier = $this->session->get('panier', []);
        $index = -1;

        // Si on ajoute une produit sans boisson incluse hors du panier
        if ($param == 'carte') {
            $ajout = -1;
            // Permet de parcourir le panier
            for ($i = 0; $i < count($panier); $i++) {
                // Permet de tester si on à déja le même produit dans le panier
                if ($panier[$i]['id'] == $id && $param == 'carte' && !$entry) {
                    // Si c'est la cas alors $ajout prend l'id du produit
                    $ajout = $i;
                };
            };
            // Si l'ajout reste à -1 alors on retourne au dernier indice du panier
            if ($ajout == -1) {
                $panier[] = ['id' => $id, 'quantite' => 1];
                $index = count($panier) - 1;
            }
            // Si $ajout = $id alors le produit existe déja dans le panier et au augmente la quantité de 1 
            else {
                $panier[$ajout]['quantite']++;
                $index = $ajout;
            };
        } 
        
        else {
            // Si on ajoute une produit avec boisson incluse
            if ($param !== 'cart') :
                // On teste si $entry existe
                if (!$entry && $entry !== '0') {
                    $panier[] = ['id' => $id, 'quantite' => 1, 'boisson' => 0];
                    $index = count($panier) - 1;
                      
                } 
                else {
                    $double = false;
                    // Permet de parcourir le panier
                    for ($i = 0; $i < count($panier); $i++) {
                        // On verifie si le produit que l'on rentre avec une boisson incluse existe déja dans le panier
                        if ($panier[$i]['id'] == $panier[$entry]['id'] && $panier[$i]['boisson'] == $id) {
                            // Si c'est le cas on augmente la quantité du menu avec boisson 1
                            $panier[$i]['quantite']++;
                            $double = true;
                        }
                    }
                    if ($double == false) {

                        $panier[$entry]['boisson'] = $id;
                        $index = $entry;
                    } 
                    else {
                        // On enlève le dernier élément du tableau
                        array_pop($panier);
                    };
                }
            else :
                // Si on ajoute un produit sans boisson en étant dans le panier
                $panier[$id - 1]['quantite']++;
            endif;
        }
        $this->session->set('panier', $panier);
        return $index;
    }

    public function delete(int $id)
    {
        // On récupère le panier
        $panier = $this->session->get('panier', []);

        // Si le produit produit existe dans le panier
        if (!empty($panier[$id-1])) {
            //On supprime ce produit du panier
            unset($panier[$id-1]);
            // On fusionne ce tableau avec lui meme ce qui va recrée un tableau qui va supprimé les indices nul
            $panier = array_merge($panier);
        }
        $this->session->set('panier', $panier);
    }

    public function getFullCart(): array
    {
        // Recuperation de l'id du produit et de la quantité
        $panier = $this->session->get('panier', []);
        $panierWithData = [];
        // On récupère dans la boucle les informations du produit que l'on a ajouté au panier
        foreach ($panier as $index => $detail) {
            // On recrée un tableau avec toutes les données du panier 
            // Une de ces actions sera effectué suivant si oui ou non il y a une boisson
            if (isset($detail['boisson'])) :
                if($detail['boisson'] == null):
                    // Dans ce cas présent si on change de page avant de choisir la boisson alors on aura une boisson choisie par défaut
                    $panierWithData[] = [
                        'product' => $this->productRepository->find($detail['id']),
                        'quantity' => $detail['quantite'],
                        'drinks' => $this->productRepository->findOneBy(['id' => 9]),
                    ];
                else:
                $panierWithData[] = [
                    'product' => $this->productRepository->find($detail['id']),
                    'quantity' => $detail['quantite'],
                    'drinks' => $this->productRepository->find($detail['boisson']),
                ];
                endif;
            else :
                $panierWithData[] = [
                    'product' => $this->productRepository->find($detail['id']),
                    'quantity' => $detail['quantite']
                ];
            endif;
        }
        return $panierWithData;
    }

    public function getTotal()
    {   
        // Récupère le total du prix du panier
        $total = 0;
        foreach ($this->getFullCart() as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
        return $total;
    }

    public function removeQuantity(int $id)
    {
        $panier = $this->session->get('panier', []);
        // Si la quantité est à 1 alors on supprime cet ID du panier
        if ($panier[$id-1]['quantite'] == 1) :
            unset($panier[$id-1]);
            // On fusionne ce tableau avec lui meme pour enlever les indices null du panier
            $panier = array_merge($panier);
        else :
            //Sinon on décrémente la quantité de 1
            $panier[$id-1]['quantite']--;
        endif;
        $this->session->set('panier', $panier);
    }

    public function destroy()
    {
        $this->session->remove('panier');
    }
}
