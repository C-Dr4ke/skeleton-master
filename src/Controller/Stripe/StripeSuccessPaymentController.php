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
        // Création de la référence de commande
        $today = date("Ymd");
        $rand = sprintf("%04d", rand(0,9999));
        $unique = $today . $rand;
        
        // Récupération du panier complet et de l'adresse de livraison
        $panier = $panierService->getFullCart();
        $address = $repository->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);

        // Création de la commande 
        $order = new Order();
        $order->setOrderNumber($unique);
        $order->setUser($this->getUser());
        $order->setDate(new \DateTime());

        // Création de la livraison
        $delivery = new Delivery();
        $delivery->setStatus("En attente de la Préparation");
        $delivery->setDeliveryDate(new \DateTime('now'));
        $order->setDelivery($delivery);

        // Creation du détail de la commande qui va récupérer les produits et quantité concerné par la commande
        foreach ($panier as $item => $value) :
            $achat = new Detail();
            $achat->setProduct($value['product']);
            $achat->setQuantity($value['quantity']);
            $achat->setOrders($order);
            $manager->merge($achat);

        endforeach;
        // On entre tout dans la BDD
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
        // Récuperation de l'adresse de livraion de l'utilisateur
        $address = $repository->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
        $numero = $address->getOrderNumber();

        // Récupération du panier complet et du total
        $panierWithData = $panierService->getFullCart();
        $total =0;
        $total = $panierService->getTotal();
        
        // Récupération des informations utilisateur
        $user = $this->getUser();
        $mail= $user->getUserIdentifier();
        
        // Création et envoie du mail de confirmation de commande
        $email = (new TemplatedEmail())
        ->from('eatstorytest@gmail.com')
        ->to($mail)
        ->subject('Confirmation de commande')
        ->htmlTemplate('stripe/emailConfirmation.html.twig');
        $cid = $email->embedFromPath('logo.png', 'logo');

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
        // Suppression du panier
        $panierService->destroy();
        $this->addFlash('success', "Merci pour votre commande, Votre plat vous sera bientot livré, vous pouvez suivre l'état de votre commande dans votre espace membre");
        return $this->redirectToRoute('home', []);

        return $this->render('stripe/emailConfirmation.html.twig', [
            'items'=>$panierWithData,
        ]);
    }
}
