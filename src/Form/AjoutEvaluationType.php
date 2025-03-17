<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Entity\Matiere;
use App\Entity\Professeur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutEvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('coef')

            ->add('statut', ChoiceType::class, [
                'choices'  => [
                    'Publiée' => 'Publiée',
                    'En cours' => 'En_cours',
                    'Enregistrée' => 'Enregistrée',
                ],
                'expanded' => false, // false = liste déroulante, true = boutons radio
                'multiple' => false, // false = choix unique, true = sélection multiple
            ])

            ->add('statut_groupe', ChoiceType::class, [
                'choices'  => [
                    'Groupe' => 'Groupe',
                    'Individuel' => 'Individuel',
                ],
                'expanded' => false, // false = liste déroulante, true = boutons radio
                'multiple' => false, // false = choix unique, true = sélection multiple
            ])

            ->add('taille_max_groupe')

            ->add('professeur', EntityType::class, [
                'class' => Professeur::class,
                'choice_label' => 'id',
                'disabled' => true,
            ])

            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'id',
                'disabled' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}
