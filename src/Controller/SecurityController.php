<?php

namespace App\Controller;

use App\Service\MailerService;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/pass-reinit',name:'app_password_reset')]
    public function passwordReinit(Request $request,
    UserRepository $userRepository,
    TokenGeneratorInterface $tokenGenerator,
    MailerService $mailer ){

        $form=$this->createForm(ResetPasswordRequestType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user=$userRepository->findOneBy(['email'=>$form->get('email')->getData()]);
            if($user){

                $token= $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $userRepository->add($user,true);

                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $mailer->sendEmail(
                    'admin@ohmygame.fr',
                    $user->getEmail(),
                    'reset_pass',
                    $url

                );

                //addmessage flash

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');



            }

        }

        return $this->render('security/reset_password_request_form.html.twig',[
            'form'=>$form
        ]);
    }

    #[Route('/pass-reinit/{token}',name:'reset_pass')]
    public function resetPass(string $token, 
    UserRepository $userRepository,
    Request $request,
    UserPasswordHasherInterface $passwordHasher ){

        $user=$userRepository->findOneBy(['resetToken'=>$token]);

        if($user){

            $form=$this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){

                $user->setResetToken('');

                $user->setPassword(
                    $passwordHasher->hashpassword(
                        $user,
                        $form->get('password')->getData()
                    )
                    );

                $userRepository->add($user,true);


                //addflash
                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('app_login');

            }

            return $this->render('security/reset_password.html.twig',
            [
                'form'=>$form
            ]);
        }

    }
}
