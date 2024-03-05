<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReviewController extends AbstractController
{


    #[Route('/jeux/{slug}/avis', name: 'app_review')]
    public function addReviewGame(Game $game, Request $request, EntityManagerInterface $entityManager, ReviewRepository $reviewRepository): Response
    {

        //call the voter
        //$this->denyAccessUnlessGranted('POST', $game);

        
        // Je créé une instance de review
        $review = new Review;

        // J'associe review a mon formulaire
        $form = $this->createForm(ReviewType::class, $review);
        
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid()) {
            
            $this->denyAccessUnlessGranted('POST', $game);

            $review->setCreatedAt(new \DateTimeImmutable('now'));
            $review->setMember($this->getUser());

            // J'associe le jeu a la review
            $game->addReview($review);
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute("app_games_details", ['id' =>$game->getId(), "slug" => $game->getSlug()]);
        }



        return $this->render('review/addReview.html.twig', [
            'reviews' => $reviewRepository->findBy(['game' => $game->getId()], ['createdAt' => 'DESC'], 6),
            'game' => $game,
            'form' => $form,
        ]);
    }
}
