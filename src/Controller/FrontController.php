<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ReportRepository;
use App\Repository\TicketRepository;
use App\Repository\VoteRepository;
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
        $fiveLastTicket = $ticketRepository->findFiveLastTicket();
        $categories = $categoryRepository->findAll();
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
            'categories' => $categories,
            'fiveLastTicket' => $fiveLastTicket
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
    public function ticketShow(TicketRepository $ticketRepository, CategoryRepository $categoryRepository, string $ticketId, CommentRepository $commentRepository, Request $request, EntityManagerInterface $manager, VoteRepository $voteRepository, ReportRepository $reportRepository): Response
    {
        $this->voteRepository = $voteRepository;
        $categories = $categoryRepository->findAll();
        $ticketId = intval($ticketId);
        $ticket = $ticketRepository->find($ticketId);
        $user = $this->getUser();
        $comments = $commentRepository->findByTicket($ticket);

        $upvotesTicket = $voteRepository->findByUpvoteTicket($ticket);
        $downvotesTicket = $voteRepository->findByDownvoteTicket($ticket);

        $commentsFinal = [];
        foreach ($comments as $comment){
            $upvotesComment = $this->voteRepository->findByUpvoteComment($comment);
            $downvotesComment = $this->voteRepository->findByDownvoteComment($comment);

            $upvoteCommentExist = $voteRepository->checkUpvoteCommentExist($comment, $user);
            $downvoteCommentExist = $voteRepository->checkDownvoteCommentExist($comment, $user);
            $reportCommentExist = $reportRepository->checkReportCommentExist($comment, $user);

            $stateVoteComment = '';
            $stateReportComment = '';

            if ($downvoteCommentExist){
                $downvoteComment = $downvoteCommentExist[0];
                if($downvoteComment->isActive() === true){
                    $stateVoteComment = 'downvote';
                }
            }
            if ($upvoteCommentExist){
                $upvoteComment = $upvoteCommentExist[0];
                if($upvoteComment->isActive() === true){
                    $stateVoteComment = 'upvote';
                }
            }
            if ($reportCommentExist){
                $reportComment = $reportCommentExist[0];
                if($reportComment->isActive() === true){
                    $stateReportComment = 'reportOn';
                }
            }


            $object = new \stdClass();
            $object->id = $comment->getId();
            $object->content = $comment->getContent();
            $object->author = $comment->getAuthor();
            $object->createdAt = $comment->getCreatedAt();
            $object->upvotes = count($upvotesComment);
            $object->downvotes = count($downvotesComment);
            $object->stateVoteComment = $stateVoteComment;
            $object->stateReportComment = $stateReportComment;

            array_push($commentsFinal, $object);
        }
        $upvoteExist = $voteRepository->checkUpvoteTicketExist($ticket, $user);
        $downvoteExist = $voteRepository->checkDownvoteTicketExist($ticket, $user);

        $stateVoteTicket = '';
        if ($downvoteExist){
            $downvote = $downvoteExist[0];
            if($downvote->isActive() === true){
                $stateVoteTicket = 'downvote';
            }
        }
        if ($upvoteExist){
            $upvote = $upvoteExist[0];
            if($upvote->isActive() === true){
                $stateVoteTicket = 'upvote';
            }
        }

        $stateReportTicket = '';
        $reportExist = $reportRepository->checkReportTicketExist($ticket, $user);
        if ($reportExist){
            $report = $reportExist[0];
            if ($report->isActive() === true){
                $stateReportTicket = 'reportOn';
            }
        }

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
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('front/ticketShow.html.twig', [
            'controller_name' => 'FrontController',
            'categories' => $categories,
            'ticket' => $ticket,
            'comments' => $commentsFinal,
            'form' => $form->createView(),
            'numberOfUpvoteTicket' => count($upvotesTicket),
            'numberOfDownvoteTicket' => count($downvotesTicket),
            'stateVoteTicket' => $stateVoteTicket,
            'stateReportTicket' => $stateReportTicket
        ]);
    }

}
