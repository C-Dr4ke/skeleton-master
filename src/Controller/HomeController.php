<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Delivery;
use App\Entity\Detail;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\DeliveryAddressType;
use App\Form\RegistrationType;
use App\Repository\AddressRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;

use App\Repository\UserRepository;
use App\Service\Panier\PanierService;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Mailer\MailerInterface;
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

    #[Route('/choixBoisson/{id}', name: 'choixBoisson')]
    public function ChoixBoisson(ProductRepository $repository, $id)
    {
    
    
     $products = $repository->findAll();
  
   
     return $this->render('home/choixBoisson.html.twig',[ 
      'products'=>$products,
      'id'=>$id
      
     ]);

    
    }
    #[Route('/ajoutBoisson/{id}/{entry}', name: 'ajoutBoisson')]
    public function ajoutBoisson(Product $product, $id, PanierService $panierService, $entry)
    {
     $param='boisson';
    
     
     $panierService->add($id, $param, $entry );
    // $panierService->getFullCart($id);

     //dd($panierService);
     return $this->redirectToRoute('Plats');
    }
    

    
}
