<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use App\Service\Panier\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    // Permet de stocker des informations en session (il faudra essayer de comprendre comment cela fonctionne avant le jury)
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    
    
    #[Route('/', name: 'home')]
    public function home(ProductRepository $repository){
       
   
        $products = $repository->findAll();
      
     

        return $this->render('home/home.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/carte', name: 'carte')]
    public function carte(CategoryRepository $repository)
    {
     $categories = $repository->findAll();
     
     return $this->render('home/carte.html.twig',[ 
      'categories'=>$categories
     ]);
    }

    #[Route('/plats', name: 'Plats')]
    public function plats(ProductRepository $repository)
    {
     
     $products = $repository->findAll();

     return $this->render('home/plats.html.twig',[ 
      'products'=>$products
     ]);
    }
    

    #[Route('/entrees', name: 'Entrees')]
    public function entrees(ProductRepository $repository)
    {
     
     $products = $repository->findAll();

     return $this->render('home/entrees.html.twig',[ 
      'products'=>$products
     ]);
    }


    #[Route('/dessert', name: 'Dessert')]
    public function dessert(ProductRepository $repository)
    {
     
     $products = $repository->findAll();

     return $this->render('home/dessert.html.twig',[ 
      'products'=>$products
     ]);
    }

    #[Route('/snack', name: 'Snack')]
    public function snack(ProductRepository $repository)
    {
     
     $products = $repository->findAll();

     return $this->render('home/snack.html.twig',[ 
      'products'=>$products
     ]);
    }

    #[Route('/boissons', name: 'Boissons')]
    public function boissons(ProductRepository $repository)
    {
     
     $products = $repository->findAll();

     return $this->render('home/boissons.html.twig',[ 
      'products'=>$products
     ]);
    }

    #[Route('/menu', name: 'Menu')]
    public function menu(ProductRepository $repository)
    {
     
     $products = $repository->findAll();

     return $this->render('home/menu.html.twig',[ 
      'products'=>$products
     ]);
    }

    #[Route('/detailProduct/{id}', name: 'detailProduct')]
    public function detailProduct(Product $product)
    {
     
    

     return $this->render('home/detailProduct.html.twig',[ 
      'product'=>$product
     ]);
    }


    //************************************************************************************/
    //*********************************PANIER ***********************************//
    //***********************************************************************************/


    #[Route('/cart', name: 'cart')]
    public function cart(PanierService $panierService)
    {
       
       
        $affiche = true;
        
     $panierWithData = $panierService->getFullCart();
 
     $total = $panierService->getTotal();

    
    //  dd($panierWithData);
     return $this->render('home/cart.html.twig',[ 
        'items'=>$panierWithData,
        'affiche' => $affiche,
        'total'=>$total
     ]);
    }

    #[Route('/addCart/{id}/{param}', name: 'addCart')]
     public function addCart($id, PanierService $panierService, $param)
     {
     
    $panierService->add($id);

    //   dd($session->get('panier'));
    
    if ($param == 'cart') {
        return $this->redirectToRoute('cart');
    } else {
        return $this->redirectToRoute('home');
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

}
