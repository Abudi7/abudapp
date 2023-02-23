<?php

namespace App\Controller;

use App\Repository\AboutmeRepository;
use App\Repository\SkillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function index(AboutmeRepository $aboutmeRepository, SkillRepository $skillRepository,AuthenticationUtils $authenticationUtils): Response
    {
        $mydaten = $aboutmeRepository->findAll();
        $skills = $skillRepository->findAll();
        return $this->render('about/index.html.twig', [
            'mydaten' => $mydaten,
            'skills' =>$skills,
            'user'=> $authenticationUtils->getLastUsername()
        ]);
    }
}
