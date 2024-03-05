<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventRequests;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class EventRequestsController extends AbstractController
{
    /**
     * EventRequest create
     * Nécessite un paramètre d'url ?status=PENDING|ACCEPTED|REFUSED
     */
    #[Route('/event/requests/{id}/new', name: 'app_event_requests_new')]
    public function new(
        Event $event,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if (count($event->getAcceptedUsers()) >= $event->getPlayersMax()) {
            $this->addFlash('danger', 'Cet événement est déjà complet');
            return $this->redirectToRoute('app_event_index');
        }

        $status = $request->get('status');

        $er = new EventRequests;
        $er->setStatus($status)
            ->setUser($this->getUser())
            ->setEvent($event);

        $em->persist($er);
        $em->flush();

        return $this->redirectToRoute('app_event_index');
    }

    /**
     * EventRequest delete
     * Nécessite un paramètre d'url ?status=PENDING|ACCEPTED|REFUSED
     */
    #[Route('/event/requests/delete/{id}', name: 'app_event_requests_delete')]
    public function delete(
        EventRequests $eventRequests,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('EVENT_REQUEST_DELETE', $eventRequests);

        $em->remove($eventRequests);
        $em->flush();

        $this->addFlash('success', 'Participation annulée');
        return $this->redirectToRoute('app_event_index');
    }

    /**
     * EventRequest change status
     * Nécessite un paramètre d'url ?status=PENDING|ACCEPTED|REFUSED
     */
    #[Route('/event/requests/{id}', name: 'app_event_requests_status')]
    public function status(
        EventRequests $eventRequests,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('EVENT_REQUEST_STATUS', $eventRequests);

        $status = $request->get('status');

        $eventRequests->setStatus($status);
        $em->flush();

        return $this->redirectToRoute('app_event_index');
    }
}
