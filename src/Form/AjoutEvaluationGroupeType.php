<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\Professeur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutEvaluationGroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taille_max_groupe')
            ->add('type_groupe', ChoiceType::class,[
                'choices' => [
                    'TP'=>'TP',
                    'TD'=>'TD',
                    'Promotion'=>'Promotion'
                ]
            ])
            ->add('formation_groupe',ChoiceType::class,[
                'choices' => [
                    'Choix'=>'Choix',
                    'Aléatoire'=>'Aléatoire',
                ]
            ])

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
