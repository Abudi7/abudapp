<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ServiceController extends AbstractController
{
      /**
     * ============================
     * Display all articles from DB
     * ============================ 
     */

    #[Route('/service', name: 'app_service')]
    public function index(ServiceRepository $serviceRepository,AuthenticationUtils $authenticationUtils): Response
    {
        $service = $serviceRepository->findAll();
        return $this->render('service/index.html.twig', [
            'services' => $service,
            'user'=>$authenticationUtils->getLastUsername()
        ]);
    }
    /**
     * ====================================================
     * Create service via Form and save in Database Service Table
     * ====================================================
     */

     #[Route('/createService', name: 'app_createService')]
    public function createService(Request $request, ManagerRegistry $doctrine,AuthenticationUtils $authenticationUtils): Response
    {
        /** 
         * Call the entity Service class 
         * Path src/Entity/Service.php
         */
        $serviceEntity = new Service();

        /** 
         * Create form for many articles
         * createForm take two parameters  
         */
        $form = $this->createForm(ServiceType::class, $serviceEntity);

        /**
         * insert record in form and save in DB using Request 
         */

        $form->handleRequest($request);

        /**
         * ask if i click on Post than it must saved in DB
         */

        if ($form->isSubmitted()) {

            /**
             * Entity Manger to save the record in DB
             */
            $em = $doctrine->getManager();

            /**
             * save image name in a new file 
             */
            
            $img = $request->files->get('service')['photo'];
           

            /**
             * change the name when it used the image 2 times to avoid 
             * the problem in future
             */
            if ($img) {
                $imgName = md5(uniqid()) . '.' . $img->getClientOriginalName();
            }

            $img->move('../public/servicesImage', $imgName);
            $serviceEntity->setImage($imgName);

            //save the record from Formla
            $em->persist($serviceEntity);
            $em->flush();

            /**
             * it should to have rediarct func to return to other Page 
             */

            return $this->redirect($this->generateUrl('app_service'));
        }

        return $this->render('service/createService.html.twig', [
            'service' => $form->createView(),
            'user' => $authenticationUtils->getLastUsername()
        ]);
    }
    /**
     * ====================================================
     * Display Service via ID record 
     * ====================================================
     */

     #[Route('/displayOneService/{id}', name: 'app_displayOneService')]
     public function displayOneService(ServiceRepository $serviceRepository ,$id,AuthenticationUtils $authenticationUtils): Response
     {
         $oneService = $serviceRepository->find($id);
         return $this->render('service/displayOneService.html.twig', [
             'service' =>$oneService ,
             'user' => $authenticationUtils->getLastUsername()
         ]);  
     }
     /**
     * ====================================================
     * Delete Service via ID record 
     * ====================================================
     */

     #[Route('/delete/{id}', name: 'app_delete')]
     public function delete(ServiceRepository $serviceRepository ,$id, ManagerRegistry $doctrine): Response
     {
        // Entity Manger to delete the record from the web app 
        $em = $doctrine->getManager();
        //choose the id after that will removed 
        $delete = $serviceRepository->find($id);

        $em->remove($delete);
        //use Flush to save the change in DB 
        $em->flush();
        //create Message 
        $this->addFlash("sucsses","This ordere is deleted");
         return $this->redirect($this->generateUrl('app_service'));
     }
}
