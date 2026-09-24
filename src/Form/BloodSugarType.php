<?php

namespace App\Form;

use App\Entity\BloodSugar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BloodSugarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', NumberType::class, [
                'label' => 'Valeur de la glycémie (mg/dL)',
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 500,
                    'placeholder' => 'Ex. 105',
                ],
            ])

            ->add('date', DateType::class, [
                'label' => 'Date de la mesure',
                'widget' => 'single_text',
            ])

            ->add('time', TimeType::class, [
                'label' => 'Heure de la mesure',
                'widget' => 'single_text',
                'input' => 'datetime',
                'with_seconds' => false,
            ])

            ->add('context', ChoiceType::class, [
                'label' => 'Contexte de la mesure',
                'required' => false,
                'placeholder' => 'Sélectionnez un contexte',
                'choices' => [
                    '🌅 Au réveil' => 'reveil',

                    '🥐 Avant le petit-déjeuner' => 'avant_petit_dejeuner',
                    '🥐 Après le petit-déjeuner' => 'apres_petit_dejeuner',

                    '🍽️ Avant le déjeuner' => 'avant_dejeuner',
                    '🍽️ Après le déjeuner' => 'apres_dejeuner',

                    '🌙 Avant le dîner' => 'avant_diner',
                    '🌙 Après le dîner' => 'apres_diner',

                    '😴 Avant le coucher' => 'coucher',

                    '🏃 Avant une activité physique' => 'avant_activite',
                    '🏃 Après une activité physique' => 'apres_activite',

                    '🩸 Contrôle ponctuel' => 'controle',

                    '📋 Autre' => 'autre',
                ],
            ])

            ->add('note', TextareaType::class, [
                'label' => 'Note personnelle',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'maxlength' => 1000,
                    'placeholder' => 'Ex. repas inhabituel, stress, fatigue, symptômes, activité physique...',
                ],
                'help' => 'Facultatif — ajoutez un élément pouvant aider à comprendre cette mesure.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BloodSugar::class,
        ]);
    }
}