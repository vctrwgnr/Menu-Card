<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(DishRepository $d): Response
    {
        $dishes = $d->findAll();
        $random = array_rand($dishes, 2);

        return $this->render('home/index.html.twig', [
            'dish1' => $dishes[$random[0]],
            'dish2' => $dishes[$random[1]],
        ]);
    }
//    #[Route('/start', name: 'app_start')]
//    public function start(): Response
//    {
//        return $this->render('home/start.html.twig', [
//            'controller_name' => 'HomeController',
//        ]);
//    }
}
