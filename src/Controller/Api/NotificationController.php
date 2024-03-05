<?php

namespace App\Controller\Api;

use DateTimeImmutable;
use App\Entity\Message;
use App\Entity\Chatting;

use App\Repository\MessageRepository;
use App\Repository\ChattingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationController extends AbstractController
{
    #[Route('/api/messagerie/{id}', name: 'app_api_messagerie')]
    public function listChatting(ChattingRepository $chattingRepository,$id): JsonResponse
    {
        $chatting=$chattingRepository->find($id);
        $messages=$chatting->getMessages();
        return $this->json($messages,Response::HTTP_OK,[],['groups'=>"message"]);
    }

    #[Route('api/messagerie/send/',name:'app_api_messagerie_send',methods:["POST"])]
    public function sendMessage(Request $request,ChattingRepository $chattingRepository,MessageRepository $messageRepository):JsonResponse{

        $jsonContent=$request->getContent();
        $jsonContent=json_decode($jsonContent,true);
        //on ajoute le message au chatting 
        $message = new Message;
        $chatting=$chattingRepository->find($jsonContent['chatting']);
        $message->setChatting($chatting);
        $message->setContent($jsonContent['messageSent']);
        $message->setSentAt(new DateTimeImmutable());
                
        $messageRepository->add($message,true);

        $chatting->addMessage($message);

        
        return $this->json("ok",Response::HTTP_OK);

    }

    /**
    *Méthode qui permet à un user de supprimer une conversation lorsque celle-ci est vide  
     */ 
    #[Route('/api/messagerie/supprimer',name:'app_profil_chatting_delete')]
    public function deleteChatting(Request $request,EntityManagerInterface $entityManager,ChattingRepository $chattingRepository):Response
    {

        $jsonContent=$request->getContent();
        $jsonContent=json_decode($jsonContent,true);
        $chatting=$chattingRepository->find($jsonContent['chattingId']);


            $entityManager->remove($chatting);
            $entityManager->flush();
            return $this->redirectToRoute('app_profil_chatting');
        

    }
}
