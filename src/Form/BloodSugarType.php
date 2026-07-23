<?php

namespace App\Form;


use App\Entity\BloodSugar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
                ],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de la mesure',
                'widget' => 'single_text',
            ])
            ->add('time', TimeType::class, [
                'label' => 'Heure de la mesure',
                'widget' => 'single_text',
                'input'=> 'datetime',
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
