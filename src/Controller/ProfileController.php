<?php

namespace App\Controller;

use App\Form\RegistrationType;
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
        // Affiche les informations de l'utilisateur connecté
        return $this->render('profile/profil.html.twig', []);
    }
   
    #[Route('/modifProfil/{id}', name: 'modifProfil')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function modifProfil(Request $request, EntityManagerInterface $manager, UserRepository $repository, $id)
    {   
        // Récupère les informations de l'utilisateur actuellement connecté
        $user = $repository->find($id);
        // Crée un formulaire qui permet de modifier les informations de l'utilisateur actuel
        $form = $this->createForm(RegistrationType::class, $user, ['edit' => true]);
        $form->handleRequest($request);
        // Si tout est correct alors on envoie en BDD
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
        // Récupère toutes les commandes passées par l'utilisateur triés de la plus récente à la moins récente
        $orders = $repository->findBy(['user' => $id],  ['id' => 'DESC']);
        return $this->render('profile/mesCommandes.html.twig', [
            'orders' => $orders,
            'titre' => 'Mes commandes',
        ]);
    }

    #[Route('/detailMaCommande/{id}', name: 'detailMaCommande')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function detailMaCommandes(OrderRepository $orderRepository, $id)
    {   
        // Récupère les informations sur la commande sur laquelle on aura cliqué
        $order = $orderRepository->find($id);
        return $this->render('profile/detailMaCommande.html.twig', [
            'order' => $order,
            'titre' => 'Suivi de ma commande',
        ]);
    }
}
