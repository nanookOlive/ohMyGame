<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Type;
use App\Entity\Theme;
use App\Entity\Author;
use App\Entity\Editor;
use App\Entity\Illustrator;
use Symfony\Component\Form\AbstractType;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\LengthValidator;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du jeu',
                'constraints' => [
                    new Length(null, 3, 50),
                ]
            ])
            ->add('minimumAge', IntegerType::class, [
                'label' => 'Age minimum',
                'constraints' => [
                    new GreaterThanOrEqual(0, null, 'Trop pas né'),
                    new LessThanOrEqual(99, null, 'Trop Vieux'),
                ]
            ])
            ->add('releasedAt', DateType::class, [
                'label' => 'Année de sortie',
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'format' => 'yyyy',
                'html5' => false,
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(new \DateTimeImmutable('01-01-1901'), null, 'Doit être supérieur à 1901'),
                    new LessThanOrEqual(new \DateTimeImmutable('now'), null, 'Doit être antérieur à cette année'),
                ]
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée de jeu (min)',
                'constraints' => [
                    new GreaterThanOrEqual(3, null, 'Trop court'),
                    new LessThanOrEqual(220, null, 'Trop long'),
                ]
            ])
            ->add('imageUrl', UrlType::class, [
                'label' => 'Url de l\'image',
                'constraints' => [
                    new Length(null, 3, 255),
                ],
                'required' => false
            ])
            ->add('playersMin', IntegerType::class, [
                'label' => 'Joueurs minimum',
                'constraints' => [
                    new GreaterThanOrEqual(1, null, 'Trop pas assez'),
                    new LessThanOrEqual(99, null, 'Trop nombreux'),
                ]
            ])
            ->add('playersMax', IntegerType::class, [
                'label' => 'Joueurs maximum',
                'constraints' => [
                    new GreaterThanOrEqual(1, null, 'Trop pas assez'),
                    new LessThanOrEqual(99, null, 'Trop Nombreux'),
                ]
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'rows' => 6
                ]
            ])
            ->add('longDescription', TextareaType::class, [
                'label' => 'Description longue',
                'attr' => [
                    'rows' => 6
                ]
            ])
            ->add('author', EntityType::class, [
                'label' => 'Auteurs',
                'class' => Author::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('illustrator', EntityType::class, [
                'label' => 'Illustrateurs',
                'class' => Illustrator::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('type', EntityType::class, [
                'label' => 'Types',
                'class' => Type::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('theme', EntityType::class, [
                'label' => 'Thèmes',
                'class' => Theme::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('editor', EntityType::class, [
                'label' => 'Editeur',
                'class' => Editor::class,
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
