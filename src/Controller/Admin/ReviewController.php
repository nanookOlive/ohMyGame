<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use App\Repository\GameRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReviewController extends AbstractController
{
    #[Route('/admin/avis', name: 'app_admin_review_index')]
    public function index(GameRepository $gameRepository, ReviewRepository $reviewRepository): Response
    {
        return $this->render('admin/review/index.html.twig', [
            'games' => $gameRepository->findAll(),
            'reviews' => $reviewRepository->findAll(),
        ]);
    }

    #[Route('/admin/avis/{id}', name: 'app_admin_review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        return $this->render('admin/review/show.html.twig', [
            'game' => $review->getGame(),
            'review' => $review,
        ]);
    }

    #[Route('/admin/avis/delete/{id}', name: 'app_admin_review_delete', methods: ['POST'])]
    public function delete(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_review_index', [], Response::HTTP_SEE_OTHER);
    }
}
