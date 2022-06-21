<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(TicketRepository $ticketRepository, CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
            'categories' => $categories
        ]);
    }

    #[Route('/tickets/{categorySlug}', name: 'ticket_filterByCategory')]
    public function ticketFilterByCategory(TicketRepository $ticketRepository, CategoryRepository $categoryRepository, string $categorySlug): Response
    {
        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->findBySlug($categorySlug);
        $categoryId = $category[0]->getId();

        $tickets = $ticketRepository->findByCategory($categoryId);

        return $this->render('front/ticketFilterByCategory.html.twig', [
            'controller_name' => 'FrontController',
            'tickets' => $tickets,
            'categories' => $categories
        ]);
    }

    #[Route('/tickets/ticket/{ticketId}', name: 'ticket_show')]
    public function ticketShow(TicketRepository $ticketRepository, CategoryRepository $categoryRepository, string $ticketId, CommentRepository $commentRepository, Request $request, EntityManagerInterface $manager): Response
    {
        $categories = $categoryRepository->findAll();
        $ticketId = intval($ticketId);
        $ticket = $ticketRepository->find($ticketId);
        $comments = $commentRepository->findByTicket($ticket);

        $comment = new Comment();
        $form = $this->createFormBuilder($comment)
            ->add('content', TextType::class, [
                'label' => 'Répondre au ticket',
                'required' => true
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Sauvegarder le commentaire',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $comment->setAuthor($this->getUser());
            $comment->setTicket($ticket);
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('front/ticketShow.html.twig', [
            'controller_name' => 'FrontController',
            'categories' => $categories,
            'ticket' => $ticket,
            'comments' => $comments,
            'form' => $form->createView()
        ]);
    }

}
