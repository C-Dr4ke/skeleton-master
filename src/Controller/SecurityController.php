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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Cookie;

class SecurityController extends AbstractController
{

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, LoginFormAuthenticator $authenticator): Response
    {
        // Si l'utilisateur est déjà connecté alors on retourne à la page d'acceuil
        if ($this->getUser()) {
        return $this->redirectToRoute('home');
        }
        // Renvoie une erreur de connection si il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        // Récupère le role de l'utilisateur si il se connecte
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
        // Crée un nouvel utilisateur
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user, ['add' => true]);
        $form->handleRequest($request);
        // Si tout est correct alors les informations sont rentrées dans la BDD
        if ($form->isSubmitted() && $form->isValid()){
            $mdp = $hasher->hashPassword($user,$form->get('password')->getData());
            $user->setPassword($mdp);
            $mail=$user->getEmail();
            $manager->persist($user);
            $manager->flush();
            
            // Mail envoyer à l'addresse mail utilisateur en cas d'inscription
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
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/passwordModif', name: 'passwordModif')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function passwordModif(UserPasswordHasherInterface $hasher, Request $request, EntityManagerInterface $manager): Response
    {
        // Création du formulaire de modification de mot de passe
        $user = $this->getUser();
        $form = $this->createForm(PaswordChangeType::class, $user, ['change' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            // Si le mot de passe saisi correspond à l'ancien password
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
            } 
            else {
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
            // Vérifie dans la BDD si il y a un mail associé à celui qu'on a entré
            $email = $request->request->get('email');
            $user = $repository->findOneBy(['email' => $email]);
            if (!$user) {
                $this->addFlash('danger', 'Aucun compte à cette adresse mail');
                return $this->redirectToRoute('resetForm');
            } 
            // Si le mail existe dans la BDD alors on lui envoie un mail de reinitialisation de mot de passe
            else {
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
        // Verifie si le token et l'id correspondent bien entre le mail et la BDD
        if ($token && $id) {
            $user = $repository->findOneBy(['token' => $token, 'id' => $id]);
            if ($user) {
                return $this->render('security/resetPassword.html.twig', [
                    'id' => $id
                ]);
            } 
            else {
                $this->addFlash('danger', 'Une erreur s\'est produit, veuiller réitérer une demande de réinitialisation');
                return $this->redirectToRoute('resetForm');
            }
        }
        // On récupère l'id et les mots de passe du formulaire
        if (!empty($_POST)) {
            $id = $request->request->get('id');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirmPassword');

            // Comparaison entre le mot de passe entré et le le confirmPassword rentré
            if ($password !== $confirmPassword) {
                $this->addFlash('danger', 'Les mots de passe ne correspondent pas');
                return $this->redirectToRoute('finalReset', [
                    'id' => $id
                ]);
            } 
            // Si tout est bon alors on entre le nouveau mot de passe dans la BDD et on remet le "token" à "null"
            else {
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
