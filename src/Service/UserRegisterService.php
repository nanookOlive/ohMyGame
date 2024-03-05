<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegisterService
{
    private $userRepository;
    private $entityManager;
    private $fileUploader;
    private $userPasswordHasher;

    /**
     * Construct de UserService
     *
     * @param UserRepository                $userRepository      UserRepository pour accéder aux données utilisateur
     * @param EntityManagerInterface        $entityManager       L'EntityManager pour persister les entités
     * @param FileUploader                  $fileUploader        Le service pour gérer l'upload de fichiers
     * @param UserPasswordHasherInterface   $userPasswordHasher  Le service pour hasher les mots de passe des utilisateurs
     */
    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Inscription d'un utilisateur.
     *
     * @param User           $user         L'utilisateur à inscrire
     * @param UploadedFile   $avatarFile   Le fichier d'avatar (peut être null)
     * @param FormInterface  $form         Le formulaire associé à l'inscription
     *
     * @return bool True si l'inscription a réussi, false si l'inscription a échoué
     */
    public function registerUser(User $user, UploadedFile $avatarFile = null, FormInterface $form): bool
    {

        // Vérifier si l'email existe déjà
        $existingEmailUser = $this->userRepository->findOneByEmail($user->getEmail());

        // Vérifier si le pseudo existe déjà
        $existingAliasUser = $this->userRepository->findOneByAlias($user->getAlias());

        // Si l'email existe, ajouter une erreur au formulaire
        if ($existingEmailUser) {
            $form->get('email')->addError(new FormError('Cet email est déjà utilisé.'));
        }

        // Si le pseudo existe, ajouter une erreur au formulaire
        if ($existingAliasUser) {
            $form->get('alias')->addError(new FormError('Ce pseudo est déjà utilisé.'));
        }

        // Si ni l'email ni le pseudo n'existe, commencer l'inscription
        if (!$existingEmailUser && !$existingAliasUser) {
            $userPasswordHasher = $this->userPasswordHasher;

            // Hasher et enregistrer le mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            // Si un fichier d'avatar est fourni, gérer l'upload et enregistrer le nom du fichier dans l'entité
            if ($avatarFile instanceof UploadedFile) {
                $avatarFileName = $this->fileUploader->upload($avatarFile);
                $user->setAvatar($avatarFileName);
            }
            else{

                $user->setAvatar('ohmygame_logo.png');
            }

            // Persiste l'utilisateur dans la BDD
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return true; // Si l'inscription a réussi
        }

        return false; // Si l'inscription a échoué
    }
}
