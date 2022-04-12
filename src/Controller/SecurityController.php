<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PaswordChangeType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
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

        if (!empty($_POST)) :

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
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher, MailerInterface $mailer)
    {

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user, ['add' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) :
            
            $mdp = $hasher->hashPassword($user,$form->get('password')->getData());
            $user->setPassword($mdp);
            $mail=$user->getEmail();
            $manager->persist($user);
            $manager->flush();
            
            $email = (new TemplatedEmail())
            ->from('eatstorytest@gmail.com')
            ->to($mail)
            ->subject('Activation de votre compte')
            ->htmlTemplate('home/email_template.html.twig');
        $cid = $email->embedFromPath('logo.jpg', 'logo');

        $email->context([
            'message' => "Bonjour, Félication pour la création de votre compte. Vous pouvez dès à présent vous connecter en cliquant sur le lien ci-dessous",
            'nom' => $user->getLastname(),
            'prenom' => $user->getFirstname(),
            'subject' => 'Activation de votre compte',
            'from' => '	eatstorytest@gmail.com',
            'cid' => $cid,
            'liens' => 'http://127.0.0.1:8001/login',
            'objectif' => 'Activer votre compte'
        ]);

        $mailer->send($email);
           

            $this->addFlash('success', 'Félicitation, votre inscription s\'est bien déroulée. Connectez vous à présent');
            return $this->redirectToRoute('login');


        endif;


        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/passwordModif', name: 'passwordModif')]
    public function passwordModif(UserPasswordHasherInterface $hasher, Request $request, EntityManagerInterface $manager): Response
    {

        $user = $this->getUser();

        $form = $this->createForm(PaswordChangeType::class, $user, ['change' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            //dd($hasher->isPasswordValid($user, $oldPassword));

            // // Si le password saisi correspond à l'ancien password
            if ($hasher->isPasswordValid($user, $oldPassword)) {

                $newPassword = $form->get('newPassword')->getData();

                $password = $hasher->hashPassword($user, $newPassword);

                $user->setPassword($password);
                // Envoi en BDD

                // Le persist permet de figer la donnée. 
                // Elle sert à figer la donnée et prépare la, à être créée dans la BDD 
                // Dans le cadre d'une mise à jour persist n'est pas nécéssaire (on peut la garder en revanche si on veut)
                // $this->entityManager->persist($user)

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Votre mot de passe a bien été mis à jour');
                return $this->redirectToRoute('profil');
            } else {
                $this->addFlash('danger', "Le mot de passe actuel saisi n'est pas le bon");
            }
        }
        return $this->render('security/passwordModif.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/resetForm', name: 'resetForm')]
    #[Route('/resetToken', name: 'resetToken')]
    public function resetForm(UserRepository $repository, Request $request, EntityManagerInterface $manager, MailerInterface $mailer)
    {
        if (!empty($_POST)) {

            $email = $request->request->get('email');
            $user = $repository->findOneBy(['email' => $email]);
            if (!$user) {
                $this->addFlash('danger', 'Aucun compte à cette adresse mail');
                return $this->redirectToRoute('resetForm');
            } else {
                $id = $user->getId();
                $token = uniqid();
                $user->setToken($token);
                $manager->persist($user);
                $manager->flush();

                $email = (new TemplatedEmail())
                    ->from('eatstorytest@gmail.com')
                    ->to($email)
                    ->subject('Demande de réinitialisation du mot de passe')
                    ->htmlTemplate('home/email_template.html.twig');
                $cid = $email->embedFromPath('logo.jpg', 'logo');

                $email->context([
                    'message' => 'Vous venez de faire une demande de réinitialisation de mot de passe, veuillez cliquer sur le lien ci-dessous afin de procéder à la reinitialisation de votre mot de passe',
                    'nom' => '',
                    'prenom' => '',
                    'subject' => 'Demande de réinitialisation du mot de passe',
                    'from' => '	eatstorytest@gmail.com',
                    'cid' => $cid,
                    'liens' => 'http://127.0.0.1:8001/resetPassword/' . $token . '/' . $id,
                    'objectif' => 'Réinitialiser le mot de passe'
                ]);

                $mailer->send($email);
                $this->addFlash('success', 'Un Email de récupération viens de vous être envoyé');
                return $this->redirectToRoute('home');
            }
        }
        return $this->render('security/resetForm.html.twig', []);
    }


    #[Route('/resetPassword/{token}/{id}', name: 'resetPassword')]
    #[Route('/finalRest', name: 'finalReset')]
    public function resetPassword(UserRepository $repository, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher, $token = null, $id = null)
    {
        if ($token && $id) {

            $user = $repository->findOneBy(['token' => $token, 'id' => $id]);

            if ($user) {




                return $this->render('security/resetPassword.html.twig', [
                    'id' => $id
                ]);
            } else {
                $this->addFlash('danger', 'Une erreur s\'est produit, veuiller réitérer une demande de réinitialisation');
                return $this->redirectToRoute('resetForm');
            }
        }
        if (!empty($_POST)) {
            $id = $request->request->get('id');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirmPassword');

            if ($password !== $confirmPassword) {
                $this->addFlash('danger', 'Les mots de passe ne correspondent pas');
                return $this->redirectToRoute('finalReset', [
                    'id' => $id
                ]);
            } else {
                $user = $repository->find($id);
                $mdp = $hasher->hashPassword($user, $password);
                $user->setPassword($mdp);
                $user->setToken('null');
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Mot de passe réinitialisé, connectez vous à présent');
                return $this->redirectToRoute('login');
            }
        }
    }
}
