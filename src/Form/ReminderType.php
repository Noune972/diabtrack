<?php

namespace App\Form;

use App\Entity\Reminder;
use App\Enum\ReminderType as ReminderTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReminderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de rappel',
                'choices' => ReminderTypeEnum::cases(),
                'choice_label' => static fn (ReminderTypeEnum $type): string =>
                    $type->icon() . ' ' . $type->label(),
                'choice_value' => static fn (?ReminderTypeEnum $type): string =>
                    $type?->value ?? '',
                'placeholder' => 'Choisissez un type de rappel',
                'attr' => [
                    'class' => 'reminder-select',
                ],
            ])

            ->add('frequency', ChoiceType::class, [
                'label' => 'Fréquence',
                'choices' => [
                    'Tous les jours' => 'daily',
                    'Jours de semaine (lundi au vendredi)' => 'weekdays',
                    'Chaque semaine' => 'weekly',
                    'Toutes les 2 semaines' => 'biweekly',
                    'Chaque mois' => 'monthly',
                    'Tous les 3 mois' => 'quarterly',
                    'Tous les 6 mois' => 'semiannual',
                    'Chaque année' => 'yearly',
                    'Une seule fois' => 'once',
                ],
                'placeholder' => 'Choisissez une fréquence',
                'attr' => [
                    'class' => 'reminder-select',
                ],
            ])

            ->add('time', TimeType::class, [
                'label' => 'Heure du rappel',
                'widget' => 'single_text',
                'input' => 'datetime',
                'with_seconds' => false,
                'attr' => [
                    'class' => 'reminder-time',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reminder::class,
        ]);
    }
}