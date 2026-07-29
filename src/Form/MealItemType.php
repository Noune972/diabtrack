<?php
// src/Form/MealItemType.php
namespace App\Form;

use App\Entity\MealItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MealItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alimentReference', AlimentReferenceAutocompleteField::class, [
                'label' => 'Aliment',
                'placeholder' => 'Rechercher un aliment...',
                'attr' => ['class' => 'aliment-select'],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité (g)',
                'attr' => ['placeholder' => '150', 'step' => '1', 'min' => 0],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MealItem::class,
        ]);
    }
}