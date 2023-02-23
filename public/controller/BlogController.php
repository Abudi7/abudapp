<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/blog', )]
class BlogController extends AbstractController
{
    /**
     * ============================
     * Display all articles from DB
     * ============================ 
     */

    #[Route('/', name: 'app_blog_index', methods: ['GET'])]
    public function index(BlogRepository $blogRepository): Response
    {
        $blog = $blogRepository->findAll();
        return $this->render('blog/index.html.twig', [
            'blogs' => $blog
        ]);
    }


    /**
     * ====================================================
     * Create Blog via Form and save in Database Blog Table
     * ====================================================
     */

    #[Route('/new', name: 'app_blog_new', methods:['GET' ,'POST'])]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        /** 
         * Call the entity Blog class 
         * Path src/Entity/Blog.php
         */
        $blogEntity = new Blog();

        /** 
         * Create form for many articles
         * createForm take two parameters  
         */
        $form = $this->createForm(BlogType::class, $blogEntity);

        /**
         * insert record in form and save in DB using Request 
         */

        $form->handleRequest($request);

        /**
         * ask if i click on Post than it must saved in DB
         */

        if ($form->isSubmitted()&& $form->isValid()) {

            /**
             * Entity Manger to save the record in DB
             */
            $entityManger = $doctrine->getManager();

            /**
             * save image name in a new file 
             */
            $img = $request->files->get('blog')['photo'];

            /**
             * change the name when it used the image 2 times to avoid 
             * the problem in future
             */
            if ($img) {
                $imgName = md5(uniqid()) . '.' . $img->getClientOriginalName();
            }

            $img->move('../public/blogImage', $imgName);
            $blogEntity->setImage($imgName);

            //save the record from Formla
            $entityManger->persist($blogEntity);
            $entityManger->flush();

            /**
             * it should to have rediarct func to return to other Page 
             */

            return $this->redirect($this->generateUrl('app_blog_index'));
        }

        return $this->render('blog/createBlog.html.twig', [
            'blogs' => $form->createView()
        ]);
    }
    /**
     * ====================================================
     * Edit Article via ID record 
     * ====================================================
     */
    #[Route('/{id}/edit', name: 'app_blog_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Blog $blog, BlogRepository $blogRepository):Response
    {
        $form = $this->createForm(BlogType::class,$blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blogRepository->save($blog, true);
            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form
        ]);
    }



    /**
     * ====================================================
     * Display Article via ID record 
     * ====================================================
     */

    #[Route('/{id}', name: 'app_blog_show', methods: ['GET'])]
    public function show(Blog $blog): Response
    {
        return $this->render('blog/show.html.twig', [
            'blog' =>$blog
        ]);  
    }


    /**
     * ====================================================
     * Delete Article via ID record 
     * ====================================================
     */

#[Route('/{id}', name: 'app_blog_delete')]
    public function delete( BlogRepository $blogRepository,$id,ManagerRegistry $doctrine): Response
    {
        
        // Entity Manger to delete the record from the web app 
        $em = $doctrine->getManager();
        //choose the id after that will removed 
        $delete = $blogRepository->find($id);

        $em->remove($delete);
        //use Flush to save the change in DB 
        $em->flush();
        //create Message 
        $this->addFlash("sucsses","This ordere is deleted");
         return $this->redirect($this->generateUrl('app_blog_index'));
     }
    
}