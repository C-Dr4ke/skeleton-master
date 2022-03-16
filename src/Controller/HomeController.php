<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
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
    public function home(ProductRepository $repository, SubCategoryRepository $repository2){
        $session = new Session();
        // $subCategories = $repository2->findAll();
        // $session->set('subCategories', $subCategories);
        $products = $repository->findAll();
        $session = $this->requestStack->getSession();
        // dd($session->get('subCategories'));
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
}
