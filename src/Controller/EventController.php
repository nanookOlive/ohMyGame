<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Form\EventType;
use App\Service\FileUploader;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/evenement')]
class EventController extends AbstractController
{
    public function __construct(
        private FileUploader $fileUploader
    ) {
    }

    /**
     * All Event
     *
     * @param EventRepository $eventRepository
     * @return Response
     */
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $city = $request->get('ville') ?: null;

        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findByFutureDate(urldecode($city)),
            'passedEvents' => $eventRepository->findByPassedDate()
        ]);
    }

    /**
     * All Event for User, as host or guest
     */
    #[Route('/mes-evenements', name: 'app_event_user', methods: ['GET'])]
    public function userEvent(EventRepository $eventRepository): Response
    {
        /** @var User */
        $user = $this->getUser();

        return $this->render('event/index_user.html.twig', [
            'events' => $eventRepository->findByHostOrGuests($user)
        ]);
    }

    /**
     * Create new event
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User */
        $user = $this->getUser();

        $event = new Event();
        $form = $this->createForm(EventType::class, $event, ['role' => $user->getRoles()[0]]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setHost($this->getUser());

            if (!$event->getAddress()) {
                $event->setAddress($user->getAddress() . ' ' . $user->getCity());
            }

            // Traitement de l'image
            if ($event->isIsPublic()) {
                // Type $imageFile comme un objet UploadedFile
                /** @var UploadedFile $imageFile */
                $imageFile = $form->get('pictureFile')->getData();
                // Si une image est uploadé
                if ($imageFile) {
                    // Utilise Service/FileUploader pour enregistrer l'image
                    $imageFileName = $this->fileUploader->upload($imageFile);
                    // Met à jour la propriété image avec le nouveau nom de l'image
                    $event->setPicture($imageFileName);
                }
            }

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        // TODO : Send email to all friends of user when he create an event

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    /**
     * Delete all events before today
     */
    #[Route('/delete/events', name: 'app_event_delete_past', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteEventsBeforeToday(EventRepository $eventRepository, EntityManagerInterface $entityManager)
    {
        $events  = $eventRepository->findByPassedDate();

        foreach ($events as $e) {
            $entityManager->remove($e);
        }
        $entityManager->flush();

        $this->addFlash('success', 'Tous les événements antérieurs à aujourd\'hui ont été supprimés');
        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Show one Event details
     */
    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * Edit an Event
     */
    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        $this->denyAccessUnlessGranted('EVENT_EDIT', $event);

        /**@var User */
        $user = $this->getUser();

        $form = $this->createForm(EventType::class, $event, ['role' => $user->getRoles()[0]]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($event->getAddress() == null) {
                $event->setAddress($user->getAddress() . ', ' . $user->getPostalCode() . ' ' . $user->getCity());
            }

            // Traitement de l'image
            if ($event->isIsPublic()) {
                // Type $imageFile comme un objet UploadedFile
                /** @var UploadedFile $imageFile */
                $imageFile = $form->get('pictureFile')->getData();
                // Si une image est uploadé
                if ($imageFile) {
                    // Supprime l'image actuelle, chemin complet de l'image
                    $filesystem->remove($this->getParameter('images_directory') . '/' . $event->getPicture());
                    // Utilise Service/FileUploader pour enregistrer l'image
                    $imageFileName = $this->fileUploader->upload($imageFile);
                    // Met à jour la propriété image avec le nouveau nom de l'image
                    $event->setPicture($imageFileName);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        // TODO : Send email to all guests when an event have changed
        // TODO : Send email to all guests when an event have changed
        // TODO : Send email to all guests when an event have changed

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    /**
     * Delete an Event
     */
    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        $this->denyAccessUnlessGranted('EVENT_EDIT', $event);

        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            if ($event->getPicture()) {
                // Supprime l'image, chemin complet de l'image
                $filesystem->remove($this->getParameter('images_directory') . '/' . $event->getPicture());
            }
            $entityManager->remove($event);
            $entityManager->flush();
        }


        // TODO : Send email to all guests when an event is delete
        // TODO : Send email to all guests when an event is delete
        // TODO : Send email to all guests when an event is delete


        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
}
