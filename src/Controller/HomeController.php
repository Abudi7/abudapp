<?php

namespace App\Controller;

use App\Repository\AboutmeRepository;
use App\Repository\BlogRepository;
use App\Repository\SkillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    #[Route('/ ', name: 'app_home')]
    public function index(AuthenticationUtils $authenticationUtils,AboutmeRepository $aboutmeRepository,SkillRepository $skillRepository, BlogRepository $blogRepository): Response
    {
       
        $aboutme = $aboutmeRepository->findAll();
        $user =  $authenticationUtils->getLastUsername();
        return $this->render('home/index.html.twig', [
            'user' => $user,
            'mydaten' =>$aboutme,
            'skills' => $skillRepository->findAll(),
            'blogs' => $blogRepository->findAll()
        ]);
    }
}