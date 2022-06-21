<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/userAccount', name: 'user-account_')]
class UserController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(CategoryRepository $categoryRepository, TicketRepository $ticketRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $user = $this->getUser();
        $username = $user->getUsername();
        $tickets = $ticketRepository->findByUserLogged($this->getUser());
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'username' => $username,
            'categories' => $categories,
            'tickets' => $tickets
        ]);
    }

    #[Route('/closeTicket/{ticketId}', name: 'close-ticket')]
    public function closeTicket(EntityManagerInterface $manager, string $ticketId, TicketRepository $ticketRepository): Response
    {
        $ticketId = intval($ticketId);
        $ticket = $ticketRepository->find($ticketId);
        $user = $this->getUser();
        if ($user === $ticket->getAuthor()){
            $ticket->setEnable(false);
            $manager->persist($ticket);
            $manager->flush();
        }
        return $this->redirectToRoute('user-account_home');
    }


    #[Route('/settings', name: 'settings')]
    public function settings(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher, CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $user = $this->getUser();
        $password = $user->getPassword();
        $username = $user->getUsername();
        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'empty_data' => '',
                'required' => false
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'empty_data' => '',
                'required' => true
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'empty_data' => '',
                'required' => true
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code Postal',
                'empty_data' => '',
                'required' => true
            ])

            ->add('number', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['maxlength => 10'],
                'required' => true
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'empty_data' => '',
                'required' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Mettre à jour mes informations'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $newPassword = $form['password']->getData();
            if ($newPassword === ''){
                $user->setPassword($password);
            } else {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $newPassword
                );
                $user->setPassword($hashedPassword);
            }
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('user-account_home');
        }

        return $this->render('user/settings.html.twig', [
            'controller_name' => 'UserController',
            'username' => $username,
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }

    #[Route('/closeAccount', name: 'close-account')]
    public function closeAccount(EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $user->setActive(false);
        $manager->persist($user);
        $manager->flush();

        $tickets = $user->getTicket();
        foreach ($tickets as $ticket){
            $ticket->setActive(false);
            $manager->persist($ticket);
            $manager->flush();
        }


        return $this->redirectToRoute('authentication_logout');
    }

    #[Route('/createTicket', name: 'create-ticket')]
    public function createTicket(EntityManagerInterface $manager, Request $request, SluggerInterface $slugger, CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $ticket = new Ticket();

        $form = $this->createFormBuilder($ticket)
            ->add('category', EntityType::class, [
                'label' => 'Category',
                'required' => true,
                'class' => Category::class,
                'choice_label' => 'title'
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true
            ])
            ->add('description', TextType::class, [
                'label' => 'Description'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer un ticket'
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $categoryChoice = $form['category']->getData();
            $ticket->setCategory($categoryChoice);
            $ticket->setAuthor($this->getUser());
            $title = $ticket->getTitle();
            $description = $ticket->getDescription();
            $slug = $slugger->slug($title . '' . $description)->lower();
            $ticket->setSlug($slug);
            $manager->persist($ticket);
            $manager->flush();
            return $this->redirectToRoute('user-account_home');
        }


        return $this->render('user/createTicket.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }
}
