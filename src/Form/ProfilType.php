<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Bibliogame;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;



class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                "label" => "Prénom",
                "constraints" => [
                    new NotNull(),
                    new NotBlank()
                ]
            ])
            ->add('lastname', TextType::class, [
                "label" => "Nom"
            ])
            ->add('alias', TextType::class, [
                'label' => "Pseudo"
            ])
            // ->add('avatar', TextType::class, [
            //     'label' => "Lien vers image d'avatar"
            // ])
            ->add('birthAt', DateType::class, [
                'label' => 'Date de naissance',
                'input' => 'datetime_immutable',
                'widget' => 'single_text'
            ])
            ->add('address', TextType::class, [
                "label" => "Adresse"
            ])
            ->add('city', TextType::class, [
                "label" => "Ville"
            ])
            ->add('postalCode', TextType::class, [
                'label' => "Code Postal"
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Avatar',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '300k',
                        'maxSizeMessage' => 'La taille du fichier ne doit pas dépasser 300 ko',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG, PNG ou GIF',
                    ]),
                ],
            ])
            // ->add('longitude', TextType::class, [
            //     'label' => "longitude"
            // ])
            // ->add('latitude', TextType::class, [
            //     'label' => "latitude"
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
