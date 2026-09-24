<?php

namespace App\Form;

use App\Entity\GlycemicTarget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GlycemicTargetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $commonOptions = [
            'html5' => true,
            'scale' => 2,
            'attr' => [
                'min' => 0,
                'max' => 5,
                'step' => '0.01',
                'placeholder' => 'Ex. 0.90',
                'inputmode' => 'decimal',
            ],
        ];

        $builder
            ->add('fastingMin', NumberType::class, array_replace_recursive($commonOptions, [
                'label' => 'Minimum à jeun / avant repas (g/L)',
            ]))
            ->add('fastingMax', NumberType::class, array_replace_recursive($commonOptions, [
                'label' => 'Maximum à jeun / avant repas (g/L)',
            ]))
            ->add('postMealMin', NumberType::class, array_replace_recursive($commonOptions, [
                'label' => 'Minimum après repas (g/L)',
            ]))
            ->add('postMealMax', NumberType::class, array_replace_recursive($commonOptions, [
                'label' => 'Maximum après repas (g/L)',
            ]))
            ->add('bedtimeMin', NumberType::class, array_replace_recursive($commonOptions, [
                'label' => 'Minimum au coucher (g/L)',
            ]))
            ->add('bedtimeMax', NumberType::class, array_replace_recursive($commonOptions, [
                'label' => 'Maximum au coucher (g/L)',
            ]));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GlycemicTarget::class,
        ]);
    }
}