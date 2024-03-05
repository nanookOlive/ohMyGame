<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\MailerService;
use App\Service\UserRegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private $userRegisterService;
    private $mailerService;

    //  Injection du service UserRegisterService et du service MailerService
    public function __construct(UserRegisterService $userRegisterService, MailerService $mailerService)
    {
        $this->userRegisterService = $userRegisterService;
        $this->mailerService = $mailerService;
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // récupère les données du champ d'avatar du formulaire
            $avatarFile = $form->get('avatarFile')->getData();

            // Ensuite: appel de la méthode registerUser du service UserRegisterService
            $userRegistered = $this->userRegisterService->registerUser($user, $avatarFile, $form);

            // Si l'utilisateur est enregistré avec succès
            if ($userRegistered) {
                // Envoie d'un mail de confirmation d'inscription à l'utilisateur
                $senderEmail = $this->getParameter('admin_mail');
                $toEmail = $form->get('email')->getData();
                $subject = "Inscription validée";
                $content = "Bonjour $user, merci de vous être inscrit(e) sur notre site. Vous pouvez maintenant vous connecter à votre profil.";

                $emailSentSuccessfully =
                    $this->mailerService->sendEmail(
                        $senderEmail,
                        $toEmail,
                        $subject,
                        $content
                    );

                if ($emailSentSuccessfully) {
                    // True => L'e-mail a été envoyé avec succès
                    $this->addFlash('success', 'Merci de vous être inscrit sur notre site Oh My Game. Un email de confirmation vous a été envoyé.');
                } else {
                    // False => L'envoi de l'e-mail a échoué
                    $this->addFlash('error', 'L\'envoi de l\'e-mail a échoué. Veuillez réessayer.');
                }

                // Rediriger l'utilisateur vers la page de connexion
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('error', 'Votre inscription a échoué. Veuillez réessayer.');
            }
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
