<?php

namespace App\Form;

use App\Entity\SportingActivity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class SportingActivityType extends AbstractType
{
    private const INPUT_CLASS = 'w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'label' => false,
                'widget' => 'single_text', // <input type="date"> natif, stylable en Tailwind directement
                'html5' => true,
                'constraints' => [
                    new NotBlank(message: 'Merci de renseigner la date.'),
                ],
                'attr' => ['class' => self::INPUT_CLASS],
            ])
            ->add('hour', TimeType::class, [
                'label' => false,
                'widget' => 'single_text', // <input type="time"> natif
                'input' => 'datetime',
                'with_seconds' => false,
                'html5' => true,
                'constraints' => [
                    new NotBlank(message: "Merci de renseigner l'heure."),
                ],
                'attr' => ['class' => self::INPUT_CLASS],
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (minutes)',
                'constraints' => [
                    new NotBlank(message: 'Merci de renseigner la durée.'),
                    new Positive(message: 'La durée doit être supérieure à 0.'),
                ],
                'attr' => [
                    'placeholder' => 'ex. 45',
                    'class' => self::INPUT_CLASS,
                ],
                'label_attr' => ['class' => 'block text-gray-800 font-medium mb-2'],
            ])
            ->add('type_of_activity', TextType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank(message: "Merci de préciser l'activité."),
                ],
                'attr' => [
                    'placeholder' => 'Baseball',
                    'class' => self::INPUT_CLASS,
                ],
            ])
            ->add('calories', IntegerType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank(message: 'Merci de renseigner les calories dépensées.'),
                    new Positive(message: 'Les calories doivent être supérieures à 0.'),
                ],
                'attr' => [
                    'placeholder' => '500 cal',
                    'class' => self::INPUT_CLASS,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SportingActivity::class,
        ]);
    }
}