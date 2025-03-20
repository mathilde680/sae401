<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Entity\Matiere;
use App\Entity\Professeur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class AjoutEvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Entrez le nom de l\'évaluation.'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nom de l\'évaluation.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Veuillez saisir au minimum {{ limit }} caractères.',
                        'max' => 255,
                        'maxMessage' => 'Veuillez saisir au maximum {{ limit }} caractères.',

                    ]),
                ]

            ])

            ->add('date', null, [
                'widget' => 'single_text',
                'label' => 'Date de l\'évaluation',
            ])

            ->add('coef', null, [
                'label' => 'Coefficient',  // label
                'attr' => ['placeholder' => 'Coefficiant'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le coefficiant de l\'évaluation.',
                    ]),
                    new Range([
                        'min' => 1,
                        'max' => 20,
                        'notInRangeMessage' => 'Le coefficient doit être compris entre {{ min }} et {{ max }}.',
                    ]),
                ]
            ])

//            ->add('statut', ChoiceType::class, [
//                'label' => 'Statut',
//                'choices'  => [
//                    'Enregistrée' => 'Enregistrée',
//                    'Publiée' => 'Publiée',
//                    'En cours' => 'En_cours',
//                ],
//                'expanded' => false, // false = liste déroulante, true = boutons radio
//                'multiple' => false, // false = choix unique, true = sélection multiple
//            ])

            ->add('statut_groupe', ChoiceType::class, [
                'label' => 'L\'évaluation est par',
                'choices'  => [
                    'Groupe' => 'Groupe',
                    'Individuel' => 'Individuel',
                ],
                'expanded' => false, // false = liste déroulante, true = boutons radio
                'multiple' => false, // false = choix unique, true = sélection multiple
            ])

//            ->add('taille_max_groupe')

//            ->add('professeur', EntityType::class, [
//                'class' => Professeur::class,
//                'choice_label' => 'id',
//                'disabled' => true,
//            ])
//
//            ->add('matiere', EntityType::class, [
//                'class' => Matiere::class,
//                'choice_label' => 'id',
//                'disabled' => true,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}
