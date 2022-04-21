<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ProfileController extends AbstractController
{
    
   
    #[Route('/profil', name: 'profil')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profil(UserRepository $repository)
    {

        

        return $this->render('profile/profil.html.twig', []);
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

        return $this->render('profile/modifProfil.html.twig', [
            'form' => $form->createView(),
            'titre' => 'Informations du profil',
            'id'=>$id
        ]);
    }

    #[Route('/mesCommandes/{id}', name: 'mesCommandes')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function mesCommandes(OrderRepository $repository, $id)
    {   
      
        $orders = $repository->findBy(['user' => $id],  ['id' => 'DESC']);
    //    dd($orders);

      

        return $this->render('profile/mesCommandes.html.twig', [
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

      

        return $this->render('profile/detailMaCommande.html.twig', [
            'order' => $order,
            'titre' => 'Suivi de ma commande',
        ]);
    }


}
