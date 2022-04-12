<?php

namespace App\Controller\Stripe;

use App\Entity\Address;
use App\Entity\Delivery;
use App\Entity\Detail;
use App\Entity\Order;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeSuccessPaymentController extends AbstractController
{
    #[Route('/stripe/success', name: 'success_url')]
    public function successURL(EntityManagerInterface $manager, PanierService $panierService, AddressRepository $repository): Response
    {
        $panier = $panierService->getFullCart();
        $address = $repository->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
        // dd($address);



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

        $delivery->setAddress($address);
        $manager->persist($order);
        $manager->persist($delivery);
        $manager->flush();
        $panierService->destroy();
        $this->addFlash('success', "Merci pour votre commande, Votre plat vous sera bientot livré, vous pouvez suivre l'état de votre commande dans votre espace membre");

        return $this->redirectToRoute('home', []);

        return $this->render('stripe/success.html.twig', []);
    }
    #[Route('/stripe/cancel', name: 'cancel_url')]
    public function cancelURL(): Response
    {
        return $this->render('stripe/cancel.html.twig', []);
    }
}
