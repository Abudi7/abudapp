<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\GalleryType;
use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
#[Route('/gallery')]
class GalleryController extends AbstractController
{
    #[Route('/', name: 'app_gallery_index')]
    public function index(AuthenticationUtils $authenticationUtils,GalleryRepository $galleryRepository): Response
    {
        return $this->render('gallery/index.html.twig', [
            'gallerys' => $galleryRepository->findAll(),
            'user'=> $authenticationUtils->getLastUsername()
        ]);
    }
    #[Route('/new', name: 'app_gallery_new')]
    public function new(Request $request,GalleryRepository $galleryRepository,AuthenticationUtils $authenticationUtils):Response
    {
        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             /**
             * save image name in a new file 
             */
            $img = $request->files->get('gallery')['image'];
               /**
             * change the name when it used the image 2 times to avoid 
             * the problem in future
             */
            if ($img) {
                $imagNew = md5(uniqid()).'.'.$img->getClientOriginalName();
            }
            $img->move('../public/galleryImage',$imagNew);
            $gallery->setImage($imagNew);

            $galleryRepository->save($gallery, true);
            return $this->redirectToRoute('app_gallery_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('gallery/new.html.twig', [
            'gallery' => $form->createView(),
            'user' => $authenticationUtils->getLastUsername(), 
        ]);
    }


}
