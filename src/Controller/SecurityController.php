<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, LoginFormAuthenticator $authenticator): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if (!empty($_POST)):

            return $authenticator->onAuthenticationSucces($this->getUser()->getRoles());
        endif;


        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }




   
    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    
    #[Route('/register', name: 'register')]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher)
    {

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):

            $mdp = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($mdp);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Félicitation, votre inscription s\'est bien déroulée. Connectez vous à présent');
            return $this->redirectToRoute('login');


        endif;


        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }




}
