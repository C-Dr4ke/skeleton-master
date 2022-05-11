<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Delivery;
use App\Entity\Detail;
use App\Entity\Order;
use App\Form\DeliveryAddressType;
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
    #[Route('orderInformations', name: 'orderInformations')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function orderInformations(PanierService $panierService, AddressRepository $repository)
    {
        // Récupération du panier complet
        $panierWithData = $panierService->getFullCart();
        // Si le panier n'existe pas alors on ne peut pas avoir accès à cette page
        if( !isset( $panierWithData)) {
            return $this->redirectToRoute('home');
        }
        // On récupère la dernière adresse utilisé par l'utilasteur lors de sa précédente commande
        $address = $repository->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
        // Intitalisation du total à 0 
        $total =0;
        // On récupère la somme totale de la commande
        $total = $panierService->getTotal();
     
        return $this->render('order/orderInformations.html.twig',[ 
            'items'=>$panierWithData,
            'total'=>$total,
            'address'=>$address
        ]);
    }

    #[Route('newDeliveryAddress', name: 'newDeliveryAddress')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function newDeliveryAddress(EntityManagerInterface $manager, Request $request, PanierService $panierService)
    {
        // Récupération du panier complet
        $panierWithData = $panierService->getFullCart();
        // Si le panier n'existe pas alors on ne peut pas avoir accès à cette page  
        if( !isset( $panierWithData)) {
            return $this->redirectToRoute('home');
        }
        // Création d'une nouvelle adresse de livraison
        $address= new Address;
        $form = $this->createForm(DeliveryAddressType::class, $address);

        $form->handleRequest($request);
        // Si toutes les informations sont correctes alors on les rentre dans la BDD
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

    //*************************************************************************************//
    //************************************** Mail ****************************************//
    //***********************************************************************************//
    
    #[Route('emailForm', name: 'emailForm')]
    #[IsGranted('ROLE_ADMIN')]
    public function emailForm()
    {
        return $this->render('home/email_form.html.twig', []);
    }

    #[Route('/emailSend', name: 'emailSend')]
    #[IsGranted('ROLE_ADMIN')]
    public function emailSend(Request $request, MailerInterface $mailer)
    {
        // On récupère les infos pour l'envoie du mail
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
