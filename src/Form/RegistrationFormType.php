<?php

namespace App\Form;

use App\Entity\Etudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Entrez votre email']
            ])

            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Entrez votre nom']
            ])

            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Entrez votre prénom']
            ])

            ->add('promotion', ChoiceType::class, [
                'choices'  => [
                    'BUT1' => 'BUT1',
                    'BUT2' => 'BUT2',
                    'BUT3' => 'BUT3',
                ],
                'placeholder' => 'Choisissez une promotion', // Optionnel : valeur par défaut vide
                'expanded' => false, // false = liste déroulante, true = boutons radio
                'multiple' => false, // false = choix unique, true = sélection multiple
            ])

            ->add('td', ChoiceType::class, [
                'choices'  => [
                    'AB' => 'AB',
                    'CD' => 'CD',
                    'EF' => 'EF',
                    'GH' => 'GH',
                    'IJ' => 'IJ',
                ],
                'placeholder' => 'Choisissez un TD', // Optionnel : valeur par défaut vide
                'expanded' => false, // false = liste déroulante, true = boutons radio
                'multiple' => false, // false = choix unique, true = sélection multiple
            ])

            ->add('tp', ChoiceType::class, [
                'choices'  => [
                    'A' => 'A',
                    'B' => 'B',
                    'C' => 'C',
                    'D' => 'D',
                    'E' => 'E',
                    'F' => 'F',
                    'G' => 'G',
                    'H' => 'H',
                    'I' => 'I',
                    'J' => 'J',
                ],
                'placeholder' => 'Choisissez un TD', // Optionnel : valeur par défaut vide
                'expanded' => false, // false = liste déroulante, true = boutons radio
                'multiple' => false, // false = choix unique, true = sélection multiple
            ])


            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['placeholder' => 'Entrez votre mot de passe', 'autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au minimum {{ limit }} caractères',
                        'max' => 30,
                        'maxMessage' => 'Votre mot de passe doit contenir au maximum {{ limit }} caractères',

                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
