<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    public function __construct(
        private MailerService $mailer
    ) {
    }

    #[Route('/contact', name: 'app_contact')]
    public function Contact(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact;

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contact->setCreatedAt(new \DateTimeImmutable('now'));

            $entityManager->persist($contact);
            $entityManager->flush();

            $senderEmail = $form->get('email')->getData();
            $toEmail = $this->getParameter('admin_mail');
            $subject = "Nouveau message de " . $form->get('alias')->getData() . ' - ' . $form->get('subject')->getData();
            $content = $form->get('description')->getData();

            $emailSentSuccessfully =
                $this->mailer->sendEmail(
                    $senderEmail,
                    $toEmail,
                    $subject,
                    $content
                );

            if ($emailSentSuccessfully) {
                // True => L'e-mail a été envoyé avec succès
                $this->addFlash('success', 'Votre message a bien été envoyé');
            } else {
                // False => L'envoi de l'e-mail a échoué
                $this->addFlash('error', 'L\'envoi de l\'e-mail a échoué. Veuillez réessayer.');
            }

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
