<?php

namespace App\Form;

use App\Entity\Hba1c;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;

class Hba1cType extends AbstractType
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
            ->add('hour', IntegerType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank(message: "Merci de renseigner l'heure."),
                    new Range(min: 0, max: 23, notInRangeMessage: "L'heure doit être comprise entre 0 et 23."),
                ],
                'attr' => [
                    'placeholder' => 'HH',
                    'class' => self::INPUT_CLASS,
                ],
            ])
            ->add('value', NumberType::class, [
                'label' => false,
                'scale' => 1,
                'html5' => true,
                'constraints' => [
                    new NotBlank(message: "Merci de renseigner le taux d'HbA1c."),
                    new Positive(message: 'Le taux doit être supérieur à 0.'),
                ],
                'attr' => [
                    'placeholder' => '5,4%',
                    'step' => '0.1',
                    'class' => self::INPUT_CLASS,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hba1c::class,
        ]);
    }
}