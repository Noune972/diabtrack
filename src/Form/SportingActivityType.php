<?php

namespace App\Form;

use App\Entity\SportingActivity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SportingActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type_of_activity')
            ->add('duration')
            ->add('calories')
            ->add('date')
            ->add('hour')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SportingActivity::class,
        ]);
    }
}
