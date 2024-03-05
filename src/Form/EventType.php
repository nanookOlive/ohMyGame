<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre *'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description *',
                'attr' => [
                    'rows' => 4
                ]
            ])
            // ->add('playersMin', NumberType::class, [
            //     'label' => 'Joueurs Min',
            //     'required' => false
            // ])
            ->add('playersMax', NumberType::class, [
                'label' => 'Joueurs Max',
                'required' => false
            ])
            ->add('startAt', DateTimeType::class, [
                'label' => 'Date de début *',
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'constraints' => [
                    new GreaterThanOrEqual(new \DateTime('today', new \DateTimeZone("Europe/Paris")), null, 'Doit être supérieur à aujourd\'hui'),
                ]
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => 'Date de fin',
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'required' => false
            ])
            // ->add('games', EntityType::class, [
            //     'label' => 'Jeux proposés',
            //     'class' => Game::class,
            //     'choice_label' => 'title',
            //     'multiple' => true,
            //     'required' => false
            // ])
        ;

        if ($options['role'] != 'ROLE_USER') {
            $builder
                ->add('address', TextType::class, [
                    'label' => 'Adresse Complète si différente ',
                    'empty_data' => "",
                    'required' => false
                ])
                ->add('isPublic', ChoiceType::class, [
                    'label' => 'Type d\'événement',
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Public' => true,
                        'Privé' => false
                    ]
                ])
                ->add('pictureFile', FileType::class, [
                    'label' => 'Image',
                    'mapped' => false,
                    'required' => false
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'role' => 'ROLE_USER'
        ]);
    }
}
