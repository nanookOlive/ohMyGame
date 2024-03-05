<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Message;
use Symfony\Component\Mercure\Update;
use App\Repository\ChattingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MercureController extends AbstractController
{
    #[Route('/mercure', name: 'app_mercure')]
    public function index(): Response
    {
        return $this->render('mercure/index.html.twig', [
            'controller_name' => 'MercureController',
        ]);
    }
/**
 * @param Publisher $publisher
 */
    #[Route('api/test',name:"app_test",methods:['POST'])]
    public function ping(PublisherInterface $publisher,Request $request,ChattingRepository $chattingRepository,EntityManagerInterface $entityManager,HubInterface $hub):JsonResponse
    {

        //on récupère le message envoyé
        $messageContent = $request->getContent();
        $messageContent=json_decode($messageContent,true);
        $chatting=$chattingRepository->find($messageContent['chatting']);
        //

        $message = new Message;
        $message->setSentAt(new DateTimeImmutable());
        $message->setContent($messageContent['alias']." dit : ".$messageContent['message']);
        $chatting->addMessage($message);
        $entityManager->persist($message);
        $entityManager->flush();

        $messages=$chatting->getMessages();
        $array=[];
        
        foreach($messages as $message){

            $array[]=$message->getContent();
        }

        $update= new Update('http://localhost/ping/'.$messageContent['chatting'],json_encode($array));

        $hub->publish($update);
     
        return $this->json('http://localhost/ping/'.$messageContent['chatting']);
    }
}
