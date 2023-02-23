<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogComments;
use App\Form\BlogCommentsType;
use App\Form\BlogType;
use App\Repository\BlogCommentsRepository;
use App\Repository\BlogRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/blog')]
class BlogController extends AbstractController
{
    #[Route('/', name: 'app_blog_index', methods: ['GET'])]
    public function index(BlogRepository $blogRepository ,AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
            'user'=>$authenticationUtils->getLastUsername()
        ]);
    }

    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BlogRepository $blogRepository,AuthenticationUtils $authenticationUtils): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

       /**
         * ask if i click on Post than it must saved in DB
         */

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * save image name in a new file 
             */
            $img = $request->files->get('blog')['image'];
            
            /**
             * change the name when it used the image 2 times to avoid 
             * the problem in future
             */
            if ($img) {
                $imgNewName = md5(uniqid()).'.'.$img->getClientOriginalName();
            }
            $img->move('../public/blogImage', $imgNewName);
            $blog->setImage($imgNewName);

            
            $blogRepository->save($blog, true);

            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
            'user' => $authenticationUtils->getLastUsername()
        ]);
    }

    // #[Route('/{id}', name: 'app_blog_show', methods: ['GET'])]
    // public function show(Blog $blog): Response
    // {

    //     return $this->render('blog/show.html.twig', [
    //         'blog' => $blog,
    //     ]);
    // }


 

    /**
     * Add Comments in Blog
     */
    
        #[Route("/{id}", name:'app_blog_show',methods: ['GET', 'POST'])]
        public function show(Blog $blog,Request $request, ManagerRegistry $doctrine,BlogCommentsRepository $blogCommentsRepository,AuthenticationUtils $authenticationUtils)
        {
            $comment = new  BlogComments();
            $comment->setBlog($blog);
            
            $form = $this->createForm(BlogCommentsType::class,$comment);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {         
                # Entity Manger To Save The Comment 
               
                $em = $doctrine->getManager();
                $em->persist($comment);
                $em->flush();


                return $this->redirectToRoute('app_blog_show',['id'=>$blog->getId()] ,Response::HTTP_SEE_OTHER);
            }
            //$comments = $doctrine->getRepository(BlogComments::class)->find(['blog'=>$blog]);
            $comments = $blogCommentsRepository->findBy(['blog' => $blog]);

            return $this->render('blog/show.html.twig',[
                'blog' => $blog,
                'comments' => $comments,
                'comment_form' => $form->createView(),
                'user' => $authenticationUtils->getLastUsername()
            ]);
        }
       
        

    /**
     * Edit Blog
     */

    #[Route('/{id}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog, BlogRepository $blogRepository,AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $request->files->get('blog')['image'];
            
            /**
             * change the name when it used the image 2 times to avoid 
             * the problem in future
             */
            if ($img) {
                $imgNewName = md5(uniqid()).'.'.$img->getClientOriginalName();
            }
            $img->move('../public/blogImage', $imgNewName);
            $blog->setImage($imgNewName);

            
            $blogRepository->save($blog, true);

            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
            'user'=>$authenticationUtils->getLastUsername()
        ]);
    }

    #[Route('/{id}', name: 'app_blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, BlogRepository $blogRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $blogRepository->remove($blog, true);
        }

        return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
