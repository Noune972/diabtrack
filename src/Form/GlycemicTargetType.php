<?php

namespace App\Form;

use App\Entity\GlycemicTarget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GlycemicTargetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fastingMin', IntegerType::class, [
                'label' => 'Minimum à jeun / avant repas',
                'attr' => [
                    'min' => 1,
                    'max' => 500,
                    'placeholder' => 'mg/dL',
                ],
            ])
            ->add('fastingMax', IntegerType::class, [
                'label' => 'Maximum à jeun / avant repas',
                'attr' => [
                    'min' => 1,
                    'max' => 500,
                    'placeholder' => 'mg/dL',
                ],
            ])
            ->add('postMealMin', IntegerType::class, [
                'label' => 'Minimum après repas',
                'attr' => [
                    'min' => 1,
                    'max' => 500,
                    'placeholder' => 'mg/dL',
                ],
            ])
            ->add('postMealMax', IntegerType::class, [
                'label' => 'Maximum après repas',
                'attr' => [
                    'min' => 1,
                    'max' => 500,
                    'placeholder' => 'mg/dL',
                ],
            ])
            ->add('bedtimeMin', IntegerType::class, [
                'label' => 'Minimum au coucher',
                'attr' => [
                    'min' => 1,
                    'max' => 500,
                    'placeholder' => 'mg/dL',
                ],
            ])
            ->add('bedtimeMax', IntegerType::class, [
                'label' => 'Maximum au coucher',
                'attr' => [
                    'min' => 1,
                    'max' => 500,
                    'placeholder' => 'mg/dL',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GlycemicTarget::class,
        ]);
    }
}