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
use App\Repository\SubCategoryRepository;
use App\Repository\UserRepository;
use App\Service\Panier\PanierService;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    

    //************************************************************************************/
    //*********************************PANIER ***********************************//
    //***********************************************************************************/


    #[Route('/cart', name: 'cart')]
    public function cart(PanierService $panierService)
    {
       
      
        $affiche = true;
        
     $panierWithData = $panierService->getFullCart();
   
    $total =0;
     $total = $panierService->getTotal();
     
    
    //  dd($panierWithData);
     return $this->render('home/cart.html.twig',[ 
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
     //************************************************************************************/
    //*********************************Profil ***********************************//
    //***********************************************************************************/

   
    #[Route('/profil', name: 'profil')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profil(UserRepository $repository)
    {

        

        return $this->render('home/profil.html.twig', []);
    }

     
    #[Route('/modifProfil/{id}', name: 'modifProfil')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function modifProfil(Request $request, EntityManagerInterface $manager, UserRepository $repository, $id)
    {   
      
        $user = $repository->find($id);
        $form = $this->createForm(RegistrationType::class, $user, ['edit' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
    
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Informations modifiées');
            return $this->redirectToRoute('profil');
        }

        return $this->render('home/modifProfil.html.twig', [
            'form' => $form->createView(),
            'titre' => 'Informations du profil',
            'id'=>$id
        ]);
    }

    #[Route('/mesCommandes/{id}', name: 'mesCommandes')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function mesCommandes(OrderRepository $repository, $id)
    {   
      
        $orders = $repository->findBy(['user' => $id]);
    //    dd($orders);

      

        return $this->render('home/mesCommandes.html.twig', [
            'orders' => $orders,
            'titre' => 'Mes commandes',
        ]);
    }

    #[Route('/detailMaCommande/{id}', name: 'detailMaCommande')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function detailMaCommandes(OrderRepository $orderRepository, $id)
    {   
      
        $order = $orderRepository->find($id);

    //    dd($orders);

      

        return $this->render('home/detailMaCommande.html.twig', [
            'order' => $order,
            'titre' => 'Suivi de ma commande',
        ]);
    }


    //************************************************************************************//
    //*********************************Commande ***********************************//
    //***********************************************************************************//

   
    #[Route('/order', name: 'order')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function order(EntityManagerInterface $manager, PanierService $panierService)
    {
        
        $panier = $panierService->getFullCart();
        



        $order = new Order();
        $order->setUser($this->getUser());
        $order->setDate(new \DateTime());

       
        

        $delivery = new Delivery();

        $delivery->setStatus("En attente de la Préparation");
        $delivery->setDeliveryDate(new \DateTime('now'));
        $order->setDelivery($delivery);

        foreach ($panier as $item => $value) :
            //dd($value['product']);
            $achat = new Detail();
            $achat->setProduct($value['product']);
            $achat->setQuantity($value['quantity']);
            $achat->setOrders($order);
            $manager->merge($achat);

        endforeach;


        $manager->persist($order);
        $manager->persist($delivery);
        $manager->flush();
        $panierService->destroy();
        $this->addFlash('success', "Merci pour votre commande, Votre plat vous sera bientot livré, vous pouvez suivre l'état de votre commande dans votre espace membre");

        return $this->redirectToRoute('home', []);
    }

    #[Route('orderInformations', name: 'orderInformations')]
    public function orderInformations(PanierService $panierService, AddressRepository $repository)
    {
        $panierWithData = $panierService->getFullCart();
        $address = $repository->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
       
        $total =0;
         $total = $panierService->getTotal();
     
     return $this->render('home/orderInformations.html.twig',[ 
        'items'=>$panierWithData,
        'total'=>$total,
        'address'=>$address
     ]);
    }

    #[Route('newDeliveryAddress', name: 'newDeliveryAddress')]
    public function newDeliveryAddress(EntityManagerInterface $manager, Request $request)
    {
    
        $address= new Address;
        $form = $this->createForm(DeliveryAddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $manager->persist($address);
            $manager->flush();
            $this->addFlash('success', 'Addresse saisie');
            return $this->redirectToRoute('orderInformations');
        }  
     
     return $this->render('home/newDeliveryAddress.html.twig',[ 
        'form' => $form->createView(),
        
     ]);
    }


  //************************************************************************************/
    //*********************************Mail ***********************************//
    //***********************************************************************************/
    
    #[Route('emailForm', name: 'emailForm')]
    public function emailForm()
    {


        return $this->render('home/email_form.html.twig', []);
    }

    #[Route('/emailSend', name: 'emailSend')]
    public function emailSend(Request $request, MailerInterface $mailer)
    {
        if (!empty($_POST)) {

            $message = $request->request->get('message');
            $nom = $request->request->get('surname');
            $prenom = $request->request->get('name');
            $motif = $request->request->get('need');
            $from = $request->request->get('email');

            $email = (new TemplatedEmail())
                ->from($from)
                ->to('eatstorytest@gmail.com')
                ->subject($motif)
                ->htmlTemplate('home/email_template.html.twig');
            $cid = $email->embedFromPath('logo.jpg', 'logo');

            $email->context([
                'message' => $message,
                'nom' => $nom,
                'prenom' => $prenom,
                'subject' => $motif,
                'from' => $from,
                'cid' => $cid,
                'liens' => 'http://127.0.0.1:8001',
                'objectif' => 'Accéder au site'
            ]);

            $mailer->send($email);

            return $this->redirectToRoute('home');
        }
    }

}
