<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Delivery;
use App\Entity\Detail;
use App\Entity\Order;
use App\Repository\AddressRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class OrderController extends AbstractController
{

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
     
     return $this->render('order/orderInformations.html.twig',[ 
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
     
     return $this->render('order/newDeliveryAddress.html.twig',[ 
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
                ->htmlTemplate('order/email_template.html.twig');
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
