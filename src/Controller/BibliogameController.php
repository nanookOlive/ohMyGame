<?php

namespace App\Controller;

use App\Entity\Game;
use DateTimeImmutable;
use App\Entity\Bibliogame;
use App\Service\MailerService;
use App\Repository\BibliogameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BibliogameController extends AbstractController
{
    public function __construct(
        private BibliogameRepository $bibliogameRepository,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * Consulter sa Bibliothèque de jeux
     *
     * @return Response
     */
    #[Route('/bibliogame', name: 'app_bibliogame')]
    public function index(): Response
    {
        /** @var User */
        $user = $this->getUser();

        return $this->render('bibliogame/index.html.twig', [
            'bibliogames' => $user->getBibliogames(),
            'myBorrowedGames' => $user->getMyBorrowedGames()

        ]);
    }

    /**
     * Ajouter un jeu à sa Bibliothèque
     *
     * @return Response
     */
    #[Route('/bibliogame/add/{gameId<\d+>}', name: 'app_bibliogame_add')]
    public function addGame(Game $gameId): Response
    {
        if (!$this->bibliogameRepository->findBy(['game' => $gameId, 'member' => $this->getUser()])) {
            $b = new Bibliogame;
            $b->setGame($gameId)
                ->setMember($this->getUser());

            $this->em->persist($b);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_bibliogame');
    }

    /**
     * Retirer un jeu de sa bibliothèque
     *
     * @return Response
     */
    #[Route('/bibliogame/remove/{gameId<\d+>}', name: 'app_bibliogame_remove')]
    public function removeGame(Game $gameId): Response
    {
        if ($this->bibliogameRepository->findBy(['game' => $gameId, 'member' => $this->getUser()])) {
            $b = $this->bibliogameRepository->findOneBy(['member' => $this->getUser(), 'game' => $gameId]);
            $this->em->remove($b);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_bibliogame');
    }

    /**
     * function pour remettre borrowedBy à null lors du retour d'un jeu
     */
    #[Route('/bibliogame/return/{id}', name: 'app_bibliogame_return')]
    public function bibliogameReturned(Bibliogame $bibliogame): Response
    {

        // call the voter 
        $this->denyAccessUnlessGranted('BIBLIOGAME_RETURN', $bibliogame);

        $bibliogame->setBorrowedBy(null);
        $bibliogame->setBorrowedAt(null);
        $this->em->flush($bibliogame);

        $this->addFlash('success','Jeu récupéré !');
        return $this->redirectToRoute('app_bibliogame');
    }

    /**
     * Create request to borrow a game
     */
    #[Route('/bibliogame/request/{id}', name: 'app_bibliogame_request')]
    public function request(Bibliogame $bibliogame, MailerService $mailer): Response
    {
        $bibliogame->setRequestBy($this->getUser());
        $this->em->flush($bibliogame);


        $mailer->sendEmail(
            $this->getParameter('admin_mail'),
            $bibliogame->getMember()->getEmail(),
            "Demande de prêt",
            $this->getUser() . ' souhaite vous emprunter votre exemplaire de ' . $bibliogame->getGame()->getTitle()

        );

        $this->addFlash('success','Votre demande de réservation a bien été effectuée !');
        return $this->render('profil/public_profil.html.twig', [
            'user' => $bibliogame->getMember()
        ]);
    }

    /**
     * méthode qui va permettre d'accepter une réservation
     */
    #[Route('/bibliogame/request/accept/{id}', name: 'app_bibliogame_request_accept')]
    public function requestAccept(Bibliogame $bibliogame, MailerService $mailer): Response
    {

        $this->denyAccessUnlessGranted('REQUEST_ACCEPT', $bibliogame);

        $userRequest = $bibliogame->getRequestBy();
        //on notifie par mail et sur la messagerie le user que ça demande est acceptée

        $mailer->sendEmail(
            $this->getParameter('admin_mail'),
            $bibliogame->getRequestBy()->getEmail(),
            "Demande acceptée",
            $this->getUser() . ' accepte de vous prêter son exemplaire de ' . $bibliogame->getGame()->getTitle()

        );        
        $this->addFlash('success',"Un mail a été envoyé à ".$bibliogame->getRequestBy()->getAlias()." !");

        $bibliogame->setRequestBy(null);
        $bibliogame->setBorrowedBy($userRequest);
        $bibliogame->setBorrowedAt(new DateTimeImmutable());
        $this->em->flush($bibliogame);


        return $this->redirectToRoute('app_bibliogame');
    }

    /**
     * méthode qui refuse la demande de réservation
     */
    #[Route('/bibliogame/request/deny/{id}', name: 'app_bibliogame_request_deny')]
    public function requestDeny(Bibliogame $bibliogame, MailerService $mailer): Response
    {

        $this->denyAccessUnlessGranted('REQUEST_DENY', $bibliogame);

        $userRequest = $bibliogame->getRequestBy();
        $this->addFlash('success',"Un mail a été envoyé à ".$bibliogame->getRequestBy()->getAlias()." !");

        $bibliogame->setRequestBy(null);

        //on notifie le user que sa demande est rejetée

        $mailer->sendEmail(
            $this->getParameter('admin_mail'),
            $userRequest->getEmail(),
            "Demande de prêt",
            $bibliogame->getMember() . ' ne souhaite pas vous prêter son exemplaire de ' . $bibliogame->getGame()->getTitle()

        );


        $this->em->flush($bibliogame);

        return $this->redirectToRoute('app_bibliogame');
    }
}
