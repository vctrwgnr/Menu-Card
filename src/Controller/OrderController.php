<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Repository\DishRepository;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Order;


class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(
            ['tablenumber' => 'table1']
        );
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }
    #[Route('/order/{id}', name: 'order')]
    public function order(Dish $dish, ManagerRegistry $doctrine): Response{
        $order = new Order();
        $order->setTablenumber("table1");
        $order->setName($dish->getName());
        $order->setOrdernumber($dish->getId());
        $order->setPrice($dish->getPrice());
        $order->setStatus("open");

        $em = $doctrine->getManager();
        $em->persist($order);
        $em->flush();
        $this->addFlash('order', $order->getName() . ' was added to the order');

        return $this->redirect($this->generateUrl('app_menu'));

    }
    #[Route('/staus/{id}, {status}', name: 'status')]
    public function status($id, $status, ManagerRegistry $doctrine): Response{
        $em = $doctrine->getManager();
        $order = $em->getRepository(Order::class)->find($id);
        $order->setStatus($status);
        $em->flush();
        return $this->redirect($this->generateUrl('app_order'));
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(ManagerRegistry $doctrine, $id, OrderRepository $o): Response{
        $em = $doctrine->getManager();
        $order = $o->find($id);
        $em->remove((object)$order);
        $em->flush();

        return $this->redirect($this->generateUrl('app_order'));

    }


}
