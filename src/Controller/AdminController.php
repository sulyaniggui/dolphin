<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;

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
        $tickets = $user->getTicket();

        return $this->render('admin/userShow.html.twig', [
            'controller_name' => 'AdminController',
            'categories' => $categories,
            'user' => $user,
            'tickets' => $tickets
        ]);
    }
}
