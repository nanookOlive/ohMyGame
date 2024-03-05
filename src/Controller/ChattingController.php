<?php

namespace App\Controller;

use DateImmutable;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Message;
use App\Entity\Chatting;
use App\Form\MessageType;
use App\Repository\ChattingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChattingController extends AbstractController
{
    #[Route('/profil/messagerie', name: 'app_profil_chatting')]
    public function index(ChattingRepository $chattingRepository): Response
    {
        /** @var User */
        $user = $this->getUser();
        $chattings = $user->getChattingsFrom();
        //on veut pouvoir aussi afficher les chattings ou user est user TO
        $chattingsTo = $user->getChattingsTo();
        foreach ($chattingsTo as $chatting) {

            $chattings->add($chatting);
        }
        return $this->render('chatting/chatting.html.twig', [
            'chattings' => $chattings
        ]);
    }

    #[Route('/profil/messagerie/{id}', name: 'app_profil_chatting_user')]
    public function chattingValidate(User $user, ChattingRepository $chattingRepository): Response
    {

        $userConnected = $this->getUser();
        $chattings = $user->getChattingsFrom();

        //on veut pouvoir aussi afficher les chattings ou user est user TO
        $chattingsTo = $user->getChattingsTo();
        foreach ($chattingsTo as $chatting) {

            $chattings->add($chatting);
        }
        
        if (empty($chattingRepository->findBy(['userFrom' => $userConnected, 'userTo' => $user]))) {


            if (empty($chattingRepository->findBy(['userFrom' => $user, 'userTo' => $userConnected]))) {

                $newChatting = new Chatting;
                $newChatting->setUserFrom($user);
                $newChatting->setUserTo($userConnected);
                $chattingRepository->add($newChatting, true);
                $chattings->add($newChatting);
            }
        };

        return $this->redirectToRoute('app_profil_chatting', [
            'chattings' => $chattings
        ]);
    }

    #[Route('/profil/messagerie/envoyer/{id}',name:'app_profil_chatting_send')]
    public function sendMessage(Request $request,Chatting $chatting,EntityManagerInterface $entityManager):Response{


        $form = $this->createForm(MessageType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $message = new Message;
            $message->setSentAt(new DateTimeImmutable());
            $message->setContent($request->request->get('content'));
            $chatting->addMessage($message);
            $entityManager->flush();


            return $this->redirectToRoute('app_profil_messagerie');
        }

        return $this->render('chatting/form_message.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/profil/messagerie/supprimer/{id}',name:'app_profil_chatting_delete')]
    public function deleteChatting(Chatting $chatting,EntityManagerInterface $entityManager,):Response
    {

            $entityManager->remove($chatting);
            $entityManager->flush();
            return $this->redirectToRoute('app_profil_chatting');
        

    }
   
}
