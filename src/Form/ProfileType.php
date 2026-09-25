<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\Gender;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
            ])

            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])

            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
            ])

            ->add('gender', null, [
                'label' => 'Sexe',
            ])

            ->add('dateOfBirth', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'html5' => true,
            ])

            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex. 06 12 34 56 78',
                ],
            ])

            ->add('adress', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Numéro et nom de rue',
                ],
            ])

            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex. 31000',
                ],
            ])

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex. Toulouse',
                ],
            ])

            ->add('weight', IntegerType::class, [
                'label' => 'Poids (kg)',
                'attr' => [
                    'min' => 1,
                    'step' => 1,
                ],
            ])

            ->add('height', IntegerType::class, [
                'label' => 'Taille (cm)',
                'required' => false,
                'attr' => [
                    'min' => 1,
                    'step' => 1,
                    'placeholder' => 'Ex. 168',
                ],
            ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}