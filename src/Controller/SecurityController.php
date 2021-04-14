<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;




class SecurityController extends AbstractController
{


    /**
     * @Route("/inscription", name="registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        //check if user is already logged in 
        //$tokenInterface = $this->get('security.token_storage')->getToken();
        //if($tokenInterface->isAuthenticated()){
           // $this->addFlash('redirect','Vous êtes déja connecté');
           // return $this->redirectToRoute('home');
       // }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        //les champs sont bindés

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('login'); //show
        }
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/login",name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
}
