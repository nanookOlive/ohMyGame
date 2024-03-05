<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Type;
use App\Entity\Theme;
use App\Entity\Editor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class GameSearchBarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => false
            ])
            ->add('type', EntityType::class, [
                'label' => 'Types',
                'class' => Type::class,
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ])
            ->add('theme', EntityType::class, [
                'label' => 'Thèmes',
                'class' => Theme::class,
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ])
            ->add('editor', EntityType::class, [
                'label' => 'Editeur',
                'class' => Editor::class,
                'multiple' => false,
                'expanded' => false,
                'required' => false
            ])
            ->add('minimumAge', NumberType::class, [
                'label' => 'Age, à partir de',
                'required' => false
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
