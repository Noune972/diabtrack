<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\Enum\ArticleStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleType extends AbstractType
{
    private const INPUT_CLASS = 'w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [new NotBlank(message: 'Merci de renseigner un titre.')],
                'attr' => ['placeholder' => "Titre de l'article", 'class' => self::INPUT_CLASS],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'constraints' => [new NotBlank(message: 'Merci de renseigner le contenu.')],
                'attr' => ['rows' => 10, 'placeholder' => "Contenu de l'article", 'class' => self::INPUT_CLASS],
            ])
            ->add('category', EntityType::class, [
                'class' => ArticleCategory::class,
                'choice_label' => 'title',
                'label' => 'Catégorie',
                'placeholder' => 'Choisir une catégorie',
                'constraints' => [new NotBlank(message: 'Merci de choisir une catégorie.')],
                'attr' => ['class' => self::INPUT_CLASS],
            ])
            ->add('status', EnumType::class, [
                'class' => ArticleStatus::class,
                'label' => 'Statut',
                'attr' => ['class' => self::INPUT_CLASS],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'html5' => true,
                'constraints' => [new NotBlank(message: 'Merci de renseigner la date.')],
                'attr' => ['class' => self::INPUT_CLASS],
            ])
            // 'author' n'est pas exposé dans le formulaire : il est renseigné
            // automatiquement depuis l'utilisateur connecté dans le contrôleur.
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}