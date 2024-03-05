<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alias', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('subject', ChoiceType::class, [
                'label' => 'Sélectionner un sujet',
                'choices' => [
                    'conditions générales' => 'general condition',
                    'signalement de bug' => 'bug reporting',
                    'problème' => 'problem',
                    'nouvelles fonctionnalités' => 'new functions',
                    'mot de passe perdu' => ' lost password',
                    'autre' => 'other',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Message',
                'attr' => [
                    'rows' => 8
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
