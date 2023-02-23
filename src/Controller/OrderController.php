<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Service;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $booking = $orderRepository->findBy(['service' => 'service Hight']);
        return $this->render('order/index.html.twig', [
            'orders' => $booking
        ]);
    }
    
  
    
    //@paramconverter selectOrder(Service $service)
    #[Route('/selectOrder/{id}' ,name: 'app_selectOrder')]
     public function selectOrder(Service $service, ManagerRegistry $doctrine)
    {
                
        $order = new Order();

        //set the daten Manuell
        $order->setService('service Hight');
        $order->setName($service->getTitle());
        $order->setOrdernumber($service->getId());
        $order->setPreis($service->getPrice());
        $order->setStatus('open');

        //Entity Manger
        $em = $doctrine->getManager();
        $em->persist($order);
        $em->flush();

        //create a message 
        $this->addFlash("Thanks you Slecet Service" , $order->getName() .' now i will send you E-mail to continau the next step');
        return $this->redirect($this->generateUrl('app_order'));


    }

    //create a new method for edit status
    #[Route('/status/{id},{status}' ,name: 'app_status')]
    public function status($status,$id ,ManagerRegistry $managerRegistry)
    {
        //entity Manger
        $em = $managerRegistry->getManager();
        //create object about Repository to serach the id
        $order = $em->getRepository(Order::class)->find($id);
        //change the status in the DB during the id
        $order->setStatus($status);
        //save the change
        $em->flush();

        return $this->redirect($this->generateUrl('app_order'));
    } 
}
