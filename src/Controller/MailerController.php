<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MailerController extends AbstractController
{
    #[Route('/mail', name: 'app_mail')]
    public function sendEmail(MailerInterface $mailerInterface, Request $request , AuthenticationUtils $authenticationUtils): Response
    {

        $emailForm = $this->createFormBuilder()
            ->add('yourName', TextType::class)
            ->add('email', EmailType::class)
            ->add('subject', TextType::class)
            ->add('messege', TextType::class)
            ->add('send_Messege', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-danger float-right'
                ]
            ])
            ->getForm();

        $emailForm->handleRequest($request);


        if ($emailForm->isSubmitted() && $emailForm->isValid()) {

            //get Data from the form in frontend 
            $input = $emailForm->getData();
            $name = ($input['yourName']);
            $Email = ($input['email']);
            $subject = ($input['subject']);
            $messege = ($input['messege']);


            $email = (new TemplatedEmail())
                ->from($Email)
                ->to('info@abdulrhman-alshalal.com')
                ->subject($subject)
                ->htmlTemplate('mailer/mail.html.twig')

                ->context([
                    'yourName' => $name,
                    'messege' => $messege
                ]);

            $mailerInterface->send($email);

            $this->addFlash('messege', 'Dear'. $name.',</br></br>
            Thank you for reaching out to us! We have received your message and will respond to you as soon as possible on this E-mail: '.$Email.' . In the meantime, please feel free to explore our website for more information about our services.
           
            Best regards,
            </br></br>
            Abdulrhmn Alshalal');

            return $this->redirect($this->generateUrl('app_mail'));
        }



        return $this->render('mailer/index.html.twig', [
            'emailForm' => $emailForm->createView(),
            'user' => $authenticationUtils->getLastUsername()
        ]);
    }
}
