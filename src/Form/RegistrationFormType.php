<?php
namespace App\Form;

use App\Entity\User;
use App\Enum\Gender;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('name', TextType::class, [
    'label' => 'Nom',
    'help' => 'Ex : Jacques',
])
->add('firstname', TextType::class, [
    'label' => 'Prénom',
    'help' => 'Ex : Sophie',
])
->add('email', EmailType::class, [
    'label' => 'Adresse e-mail',
    'help' => 'Ex : exemple@email.fr',
])
->add('gender', ChoiceType::class, [
    'label' => 'Genre',
    'choices' => [
        'Homme' => Gender::MAN,
        'Femme' => Gender::WOMAN,
        'Autre' => Gender::OTHER,
    ],
    'placeholder' => 'Sélectionnez votre genre',
    'help' => 'Ex : Féminin',
])
->add('weight', NumberType::class, [
    'label' => 'Poids (kg)',
    'help' => 'Ex : 75 kg',
])
->add('dateOfBirth', DateType::class, [
    'label' => 'Date de naissance',
    'widget' => 'single_text',
    'help' => 'Ex : 14/04/1985',
])
->add('plainPassword', PasswordType::class, [
    'mapped' => false,
    'label' => 'Mot de passe',
    'attr' => ['autocomplete' => 'new-password'],
    'help' => '8 caractères minimum',
    'constraints' => [
        new NotBlank(message: 'Veuillez saisir un mot de passe.'),
        new Length(
            min: 8,
            minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
            max: 4096,
        ),
    ],
])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => "J'ai lu et j'accepte les mentions légales et les CGU",
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter les mentions légales et les CGU.',
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}