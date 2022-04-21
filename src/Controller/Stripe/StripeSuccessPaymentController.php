<?php

namespace App\Controller\Stripe;


use App\Entity\Delivery;
use App\Entity\Detail;
use App\Entity\Order;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class StripeSuccessPaymentController extends AbstractController
{
    #[Route('/stripe/success', name: 'success_url')]
    public function successURL(EntityManagerInterface $manager, PanierService $panierService, AddressRepository $repository): Response
    {   
        $today = date("Ymd");
        $rand = sprintf("%04d", rand(0,9999));
        $unique = $today . $rand;
        
        $panier = $panierService->getFullCart();
        $address = $repository->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);

        $order = new Order();
        $order->setOrderNumber($unique);
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
        // dd($address->getId());
   
        $delivery->setAddress($address);
        
        $manager->persist($order);
   
        $manager->persist($delivery);
        
        $manager->flush();
      
        return $this->redirectToRoute('emailConfirmation', []);

        return $this->render('stripe/success.html.twig', []);
    }
    #[Route('/stripe/cancel', name: 'cancel_url')]
    public function cancelURL(): Response
    {
        return $this->render('stripe/cancel.html.twig', []);
    }


    #[Route('/stripe/emailConfirmation', name: 'emailConfirmation')]
    public function emailConfirmation(PanierService $panierService,MailerInterface $mailer,OrderRepository $repository): Response
    {
        
        $address = $repository->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
        $numero = $address->getOrderNumber();
        $panierWithData = $panierService->getFullCart();
        $total =0;
        $total = $panierService->getTotal();
        
        
        $user = $this->getUser();
        $mail= $user->getEmail();
 
        $email = (new TemplatedEmail())
        ->from('eatstorytest@gmail.com')
        ->to($mail)
        ->subject('Confirmation de commande')
        ->htmlTemplate('stripe/emailConfirmation.html.twig');
    $cid = $email->embedFromPath('logo.jpg', 'logo');

    $email->context([
        'nom' => $user->getLastname(),
        'prenom' => $user->getFirstname(),
        'from' => '	eatstorytest@gmail.com',
        'cid' => $cid,
        'liens' => 'http://127.0.0.1:8001/profil',
        'objectif' => 'Confirmation de commande',
        'items'=>$panierWithData,
        'total'=>$total,
        'order'=>$numero
    ]);

        $mailer->send($email);


        $panierService->destroy();
        $this->addFlash('success', "Merci pour votre commande, Votre plat vous sera bientot livré, vous pouvez suivre l'état de votre commande dans votre espace membre");

        
        return $this->redirectToRoute('home', []);

        return $this->render('stripe/emailConfirmation.html.twig', [
            'items'=>$panierWithData,
        ]);
    }
}
