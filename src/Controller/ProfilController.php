<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilType;
use App\Form\NewPasswordType;
use App\Service\FileUploader;
use App\Repository\UserRepository;
use App\Repository\BibliogameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfilController extends AbstractController
{
    /**
     * controller qui va envoyer sur la page profil d'un utilisateur
     * doivent apparaitre 
     * avatar
     * info général
     * emprunts en cours
     * jeux prêtés
     * 
     */

    #[Route('/profil', name: 'app_profil')]
    public function showPrivateProfil(UserRepository $userRepository, BibliogameRepository $bibliogameRepository): Response
    {
        /** @var User */
        $user = $this->getUser();
        //je crée un user depuis la base
        //on récupère les jeux que le user a emprunté 
        $borrowedBy = [];
        foreach ($user->getBibliogames() as $bibliogame) {
            if ($bibliogame->getBorrowedBy()) {
                $borrowedBy[] = $bibliogame;
            }
        }
        return $this->render('profil/private_profil.html.twig', [
            'user' => $user,
            'borrowedBy' => $borrowedBy
        ]);
    }

    #[Route('/profil/joueur/{id}', name: 'app_profil_public')]
    public function showPublicProfil(User $user)
    {

        return $this->render('profil/public_profil.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/profil/modifier', name: "app_profil_update")]
    public function updateProfil(Request $request, UserRepository $userRepository, FileUploader $fileUploader)
    {
        /** @var User */
        $user = $this->getUser();

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $avatarFile = $form->get('avatar')->getData();
            // vérifie si la variable $avatarFile est une instance de la classe UploadedFile
            if ($avatarFile instanceof UploadedFile) {
                // utilise le service FileUploader pour gérer l'upload du fichier. Renvoie le nom du fichier après l'upload 
                $avatarFileName = $fileUploader->upload($avatarFile);

                // fichier d'avatar sera enregistré dans la base de données pour l'utilisateur.
                $user->setAvatar($avatarFileName);
            }

            $userRepository->add($user, TRUE);
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/profil_update.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }

    #[Route('/profil/supprimer/{id}',name:'app_profil_delete',methods:['POST'])]
    public function deleteProfil(Request $request, User $user, EntityManagerInterface $entityManager){

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->container->get('security.token_storage')->setToken(null);
            $entityManager->remove($user);
            $entityManager->flush();
        }
        $this->addFlash('deleted','Votre compte a été supprimé.');
        return $this->redirectToRoute('app_logout');
    }

    
    #[Route(path: '/profil/changer-de-mot-de-passe', name: 'app_profil_edit_password')]
    #[IsGranted('ROLE_USER')]
    public function editPassword(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ) {
        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(NewPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form["newPassword"]->getData();
            $hash = $hasher->hashPassword($user, $password);
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Le mot de passe à été changé');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/edit_password.html.twig', [
            'form' => $form
        ]);
    }
}
