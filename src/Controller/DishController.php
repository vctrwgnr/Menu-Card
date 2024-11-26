<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Form\DishType;
use App\Repository\DishRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/dish', name: 'dish.')]
class DishController extends AbstractController
{

    #[Route('/', name: 'edit')]
    public function index(DishRepository $d): Response
    {
        $dishes = $d->findAll();
        return $this->render('dish/index.html.twig', [
            'dishes' => $dishes
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(ManagerRegistry $doctrine, Request $request): Response{
        $dish = new Dish();
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $image = $request->files->get('dish')['attachment'];
            if($image){
                $filename = md5(uniqid()).'.'.$image->guessExtension();
            }
            $image->move(
                $this->getParameter('images_directory'),
                $filename

            );
            $dish->setImage($filename);
            $em->persist($dish);
            $em->flush();
            return $this->redirect($this->generateUrl('dish.edit'));
        }


        return $this->render('dish/create.html.twig', [
            'createForm' => $form->createView()
        ]);

    }
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(ManagerRegistry $doctrine, $id, DishRepository $d): Response{
        $em = $doctrine->getManager();
        $dish = $d->find($id);
        $em->remove((object)$dish);
        $em->flush();

        $this->addFlash('success', 'Dish has been deleted.');
        return $this->redirect($this->generateUrl('dish.edit'));

    }
    #[Route('/show/{id}', name: 'show')]
    public function show(Dish $dish): Response{
        return $this->render('dish/show.html.twig', [
            'dish' => $dish
        ]);
    }

    #[Route('/edit/{id}', name: 'app_edit')]
    public function update(ManagerRegistry $doctrine, Request $request, Dish $dish): Response
    {
        // Create a form for the existing Dish entity
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        // Check if the form was submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();

            // Handle new image if uploaded
            $image = $request->files->get('dish')['attachment'];
            if ($image) {
                $filename = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $filename
                );

                // Update the image field only if a new file was uploaded
                $dish->setImage($filename);
            }

            // Save the updated dish entity
            $em->flush();

            // Add a flash message and redirect
            $this->addFlash('success', 'Dish has been updated successfully.');
            return $this->redirect($this->generateUrl('dish.edit', ['id' => $dish->getId()]));

        }

        // Render the form view for editing
        return $this->render('dish/edit.html.twig', [
            'editForm' => $form->createView()
        ]);
    }

}
