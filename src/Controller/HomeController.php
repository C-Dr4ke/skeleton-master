<?php

namespace App\Controller;


use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Panier\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(ProductRepository $repository){
        // Récupère tout le contenu de la table "Product"
        $products = $repository->findAll();
        return $this->render('home/home.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/carte', name: 'carte')]
    public function carte(CategoryRepository $repository)
    {
    // Récupère tout le contenu de la table "Category"
     $categories = $repository->findAll();
     
     return $this->render('home/carte.html.twig',[ 
      'categories'=>$categories
     ]);
    }

    #[Route('/plats', name: 'Plats')]
    public function plats(ProductRepository $repository)
    {
     // Récupère tout le contenu de la table "Product"
     $products = $repository->findAll();
     return $this->render('home/plats.html.twig',[ 
      'products'=>$products
     ]);
    }
    
    #[Route('/entrees', name: 'Entrees')]
    public function entrees(ProductRepository $repository)
    {
     // Récupère tout le contenu de la table "Product"
     $products = $repository->findAll();
     return $this->render('home/entrees.html.twig',[ 
      'products'=>$products
     ]);
    }

    #[Route('/dessert', name: 'Dessert')]
    public function dessert(ProductRepository $repository)
    {
     // Récupère tout le contenu de la table "Product"
     $products = $repository->findAll();
     return $this->render('home/dessert.html.twig',[ 
      'products'=>$products
     ]);
    }

    #[Route('/snack', name: 'Snack')]
    public function snack(ProductRepository $repository)
    {
     // Récupère tout le contenu de la table "Product"
     $products = $repository->findAll();
     return $this->render('home/snack.html.twig',[ 
      'products'=>$products
     ]);
    }

    #[Route('/boissons', name: 'Boissons')]
    public function boissons(ProductRepository $repository)
    {
     // Récupère tout le contenu de la table "Product"
     $products = $repository->findAll();
     return $this->render('home/boissons.html.twig',[ 
      'products'=>$products
     ]);
    }

    #[Route('/menu', name: 'Menu')]
    public function menu(ProductRepository $repository)
    {
     // Récupère tout le contenu de la table "Product"
     $products = $repository->findAll();
     return $this->render('home/menu.html.twig',[ 
      'products'=>$products
     ]);
    }

    #[Route('/detailProduct/{id}', name: 'detailProduct')]
    public function detailProduct(Product $product)
    {
     // Renvoi dans la page twig les informations du produit sur lequel on aura cliqué
     return $this->render('home/detailProduct.html.twig',[ 
      'product'=>$product
     ]);
    }

    #[Route('/choixBoisson/{id}', name: 'choixBoisson')]
    public function ChoixBoisson(ProductRepository $repository, $id)
    {
    // Récupère tout le contenu de la table "Product"
     $products = $repository->findAll();
     return $this->render('home/choixBoisson.html.twig',[ 
      'products'=>$products,
      'id'=>$id
     ]);
    }

    #[Route('/ajoutBoisson/{id}/{entry}', name: 'ajoutBoisson')]
    public function ajoutBoisson(Product $product, $id, PanierService $panierService, $entry)
    {
    // Ajoute la boisson incluse dans le menu au panier
     $param ='boisson';
     $panierService->add($id, $param, $entry );
     return $this->redirectToRoute('carte');
    }
    

    
}
