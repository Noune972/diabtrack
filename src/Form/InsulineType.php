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
            ->add('type_of_insuline', EnumType::class, [
                'class' => InsulineTypeEnum::class,
                'label' => false,
                'placeholder' => 'Lente',
                'constraints' => [
                    new NotBlank(message: "Merci de préciser le type d'insuline."),
                ],
                'attr' => ['class' => self::INPUT_CLASS . ' text-gray-500'],
            ])
            ->add('dose', NumberType::class, [
                'label' => false,
                'scale' => 2,
                'html5' => true,
                'constraints' => [
                    new NotBlank(message: 'Merci de renseigner la dose.'),
                    new Positive(message: 'La dose doit être supérieure à 0.'),
                ],
                'attr' => [
                    'placeholder' => '25 U',
                    'step' => '0.5',
                    'class' => self::INPUT_CLASS,
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