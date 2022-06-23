<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin', name: 'admin-account_')]
class AdminController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(CategoryRepository $categoryRepository, UserRepository $userRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $users = $userRepository->findAll();
        $users = array_filter($users, function($user){
            return $user !== $this->getUser();
        });
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'categories' => $categories,
            'users' => $users
        ]);
    }

    #[Route('/users/user/{userId}', name: 'user-show')]
    public function userShow(CategoryRepository $categoryRepository, UserRepository $userRepository, string $userId, TicketRepository $ticketRepository): Response
    {
        $userId = intval($userId);
        $categories = $categoryRepository->findAll();
        $user = $userRepository->find($userId);
        $ticketsActive = $ticketRepository->findTicketActiveByUser($user);
        $ticketsInactive = $ticketRepository->findTicketNotActiveByUser($user);

        return $this->render('admin/userShow.html.twig', [
            'categories' => $categories,
            'user' => $user,
            'tickets' => $ticketsActive,
            'ticketsInactive' => $ticketsInactive
        ]);
    }

    #[Route('/users/user/close/{userId}', name: 'user-closeAccount')]
    public function userCloseAccount(string $userId, UserRepository $userRepository, EntityManagerInterface $manager, Request $request): Response
    {
        $userId = intval($userId);
        $user = $userRepository->find($userId);
        $user->setActive(false);
        $tickets = $user->getTicket();
        foreach ($tickets as $ticket){
            $ticket->setActive(false);
            $manager->persist($ticket);
            $manager->flush();
        }

        $manager->persist($user);
        $manager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/users/user/open/{userId}', name: 'user-openAccount')]
    public function userOpenAccount(string $userId, UserRepository $userRepository, EntityManagerInterface $manager, Request $request): Response
    {
        $userId = intval($userId);
        $user = $userRepository->find($userId);
        $user->setActive(true);
        $tickets = $user->getTicket();
        foreach ($tickets as $ticket){
            $ticket->setActive(true);
            $manager->persist($ticket);
            $manager->flush();
        }
        $manager->persist($user);
        $manager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/users/user/ticket/delete/{ticketId}', name: 'user-deleteTicket')]
    public function userDeleteTicket(string $ticketId, TicketRepository $ticketRepository, EntityManagerInterface $manager, Request $request): Response
    {
        $ticketId = intval($ticketId);
        $ticket = $ticketRepository->find($ticketId);
        $ticket->setActive(false);
        $manager->persist($ticket);
        $manager->flush();
        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/users/user/ticket/restore/{ticketId}', name: 'user-restoreTicket')]
    public function userRestoreTicket(string $ticketId, TicketRepository $ticketRepository, EntityManagerInterface $manager, Request $request): Response
    {
        $ticketId = intval($ticketId);
        $ticket = $ticketRepository->find($ticketId);
        $ticket->setActive(true);
        $manager->persist($ticket);
        $manager->flush();
        return $this->redirect($request->headers->get('referer'));
    }


    #[Route('/createCategory', name: 'create-category')]
    public function createCategory(EntityManagerInterface $manager, CategoryRepository $categoryRepository, Request $request, SluggerInterface $slugger): Response
    {
        $categories = $categoryRepository->findAll();
        $category = new Category();

        $form = $this->createFormBuilder($category)
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer une categorie'
            ])
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $title = $form['title']->getData();
            $category->setSlug($slugger->slug($title)->lower());
            $manager->persist($category);
            $manager->flush();
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('admin/createCategory.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

}
