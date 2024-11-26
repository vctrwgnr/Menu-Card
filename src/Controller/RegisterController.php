<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Import this
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Import this
use Symfony\Component\Form\Extension\Core\Type\RepeatedType; // Import this
use Symfony\Component\Form\Extension\Core\Type\SubmitType; // Import this
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine,): Response
    {
        $registerform = $this->createFormBuilder()
            ->add('username', TextType::class, ['label' => 'Employee'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Password'], // Corrected 'first_option' to 'first_options'
                'second_options' => ['label' => 'Confirm Password'], // Corrected 'second_option' to 'second_options'
            ])
            ->add('save', SubmitType::class, ['label' => 'Register'])
            ->getForm();
        $registerform->handleRequest($request);
        if($registerform->isSubmitted() && $registerform->isValid()) {
            $input = $registerform->getData();
            $user = new User();
            $user->setUsername($input['username']);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $input['password']));
            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('home'));

        }

        return $this->render('register/index.html.twig', [
            'registerform' => $registerform->createView(),
        ]);
    }
}
