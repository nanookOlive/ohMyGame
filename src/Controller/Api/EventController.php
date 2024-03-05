<?php

namespace App\Controller\Api;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * Route qui retourne la liste des événements au format JSON
     * 
     * @param EventRepository $eventRepository
     * @return JsonResponse
     */
    
    #[Route('/api/events', name: 'app_api_events', methods: ["GET"])]
    public function events(EventRepository $eventRepository): JsonResponse
    {
        $events = $eventRepository->findAll();

        return $this->json($events, Response::HTTP_OK, [], ["groups" => "events"]);
    }
}
