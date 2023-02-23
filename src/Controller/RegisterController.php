<?php

/**
 * created by: Abdulrhman Alshalal
 * Classe Rrgister Controll
 * 24.01.2023
 */
namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request , ManagerRegistry $doctrine,UserPasswordHasherInterface $userPasswordHasherInterface,AuthenticationUtils $authenticationUtils): Response
    {
        /**
         * Create manuel register form 
         */
        $registerForm = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('username', TextType::class, ['label' => 'Username'])
            ->add(
                'plainPassword', RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password')
                )
            )

            ->add('register', SubmitType::class)
            ->getForm()
        ;

        /**
         * Useing handelRequest function
         */

        $registerForm->handleRequest($request);
        //Use if to save the daten from new user in DB
        if ($registerForm->isSubmitted()) {
            //use getData to get the daten from label 
            $input = $registerForm->getData();
            //var_dump($input);
            // make the password Hash
            $user = new User();
            $user->setEmail($input['email']);
            $user->setUsername($input['username']);
            //here the password is hashed
            $user->setPassword($userPasswordHasherInterface->hashPassword($user, $input['plainPassword']));

            //Entity Manger 

            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();
         
            //after the register it come back to the home seite
            return $this->redirect($this->generateUrl('app_home'));
        }


        return $this->render('register/index.html.twig', [
            'registerForm' => $registerForm->createView(),
            'user'=>$authenticationUtils->getLastUsername()
        ]);
    }
}