<?php

namespace App\Form;

use App\Entity\Insuline;
use App\Enum\InsulineType as InsulineTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;

class InsulineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type_of_insuline', EnumType::class, [
                'class' => InsulineTypeEnum::class,
                'label' => "Type d'insuline",
                'placeholder' => 'Choisir un type',
                'constraints' => [
                    new NotBlank(message: "Merci de préciser le type d'insuline."),
                ],
                'attr' => [
                    'class' => 'w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500',
                ],
            ])
            ->add('dose', NumberType::class, [
                'label' => 'Dose (en unités)',
                'scale' => 2,
                'html5' => true,
                'constraints' => [
                    new NotBlank(message: 'Merci de renseigner la dose.'),
                    new Positive(message: 'La dose doit être supérieure à 0.'),
                ],
                'attr' => [
                    'placeholder' => 'ex. 12.5',
                    'step' => '0.5',
                    'class' => 'w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500',
                ],
            ])
            ->add('date', DateType::class, [
                'label' => "Date de l'injection",
                'widget' => 'single_text',
                'input' => 'datetime',
                'constraints' => [
                    new NotBlank(message: 'Merci de renseigner la date.'),
                ],
                'attr' => [
                    'class' => 'w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500',
                ],
            ])
            ->add('hour', IntegerType::class, [
                'label' => "Heure de l'injection (0-23)",
                'constraints' => [
                    new NotBlank(message: "Merci de renseigner l'heure."),
                    new Range(min: 0, max: 23, notInRangeMessage: "L'heure doit être comprise entre 0 et 23."),
                ],
                'attr' => [
                    'placeholder' => 'ex. 8',
                    'min' => 0,
                    'max' => 23,
                    'class' => 'w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Insuline::class,
        ]);
    }
}