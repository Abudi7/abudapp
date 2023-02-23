<?php

namespace App\Controller;

use App\Entity\Portfolio;
use App\Form\PortfolioType;
use App\Repository\PortfolioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/portfolio')]
class PortfolioController extends AbstractController
{
    #[Route('/', name: 'app_portfolio_index', methods: ['GET'])]
    public function index(PortfolioRepository $portfolioRepository,AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('portfolio/index.html.twig', [
            'portfolios' => $portfolioRepository->findAll(),
            'user'=>$authenticationUtils->getLastUsername()
        ]);
    }

    #[Route('/new', name: 'app_portfolio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PortfolioRepository $portfolioRepository,AuthenticationUtils $authenticationUtils): Response
    {
        $portfolio = new Portfolio();
        $form = $this->createForm(PortfolioType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $img = $request->files->get('portfolio')['image'];

             /**
             * change the name when it used the image 2 times to avoid 
             * the problem in future
             */
                if ($img) {
                $imgName = md5(uniqid()) . '.' . $img->getClientOriginalName();
                }
            $img->move('../public/portfolioImage',$imgName);
            $portfolio->setImage($imgName);


            $portfolioRepository->save($portfolio, true);

            return $this->redirectToRoute('app_portfolio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('portfolio/new.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form,
            'user'=>$authenticationUtils->getLastUsername()
        ]);
    }

    #[Route('/{id}', name: 'app_portfolio_show', methods: ['GET'])]
    public function show(Portfolio $portfolio,AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('portfolio/show.html.twig', [
            'portfolio' => $portfolio,
            'user'=>$authenticationUtils->getLastUsername()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_portfolio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Portfolio $portfolio, PortfolioRepository $portfolioRepository,AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(PortfolioType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $portfolioRepository->save($portfolio, true);

            return $this->redirectToRoute('app_portfolio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('portfolio/edit.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form,
            'user'=>$authenticationUtils->getLastUsername()
        ]);
    }

    #[Route('/{id}', name: 'app_portfolio_delete', methods: ['POST'])]
    public function delete(Request $request, Portfolio $portfolio, PortfolioRepository $portfolioRepository,AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isCsrfTokenValid('delete'.$portfolio->getId(), $request->request->get('_token'))) {
            $portfolioRepository->remove($portfolio, true);
        }

        return $this->redirectToRoute('app_portfolio_index', ['user' =>$authenticationUtils->getLastUsername() ], Response::HTTP_SEE_OTHER);
    }
}
