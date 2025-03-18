<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\FicheMatiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheMatiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'nom', // Affiche le nom de la matière dans la liste déroulante
                'placeholder' => 'Sélectionnez une matière',
                'label' => 'Matière',
                'required' => true,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FicheMatiere::class,
        ]);
    }
}
