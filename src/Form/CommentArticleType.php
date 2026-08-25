<?php

namespace App\Form;

use App\Entity\CommentArticle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank(message: 'Merci de saisir un commentaire.'),
                    new Length(max: 1000, maxMessage: 'Votre commentaire ne peut pas dépasser {{ limit }} caractères.'),
                ],
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Votre commentaire...',
                    'class' => 'w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600',
                ],
            ])
            // date, hour, status, patient et article sont fixés côté contrôleur,
            // jamais laissés au choix de l'utilisateur.
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommentArticle::class,
        ]);
    }
}