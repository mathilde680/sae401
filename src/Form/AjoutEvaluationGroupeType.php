<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\Professeur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class AjoutEvaluationGroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taille_max_groupe', IntegerType::class, [
                'label' => 'Nombre maximum d\'étudiants par groupe',
                'attr' => [
                    'min' => 1
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 255,
                        'notInRangeMessage' => 'Le nombre d\'étudiants par groupe doit être compris entre {{ min }} et {{ max }}',
                    ]),
                ]
            ])
            ->add('type_groupe', ChoiceType::class, [
                'choices' => [
                    'TP' => 'TP',
                    'TD' => 'TD',
                    'Promotion' => 'Promotion'
                ],
                'label' => 'Type de groupe'
            ])
            ->add('formation_groupe', ChoiceType::class, [
                'choices' => [
                    'Choix' => 'Choix',
                    'Aléatoire' => 'Aléatoire',
                ],
                'label' => 'Méthode de formation des groupes'
            ]);

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
            // Configure your form options here
        ]);
    }
}
