<?php
namespace App\Form;

use App\Entity\FicheGrille;
use App\Entity\Grille;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('grille', EntityType::class, [
                'class' => Grille::class,
                'choice_label' => 'nom',
                'expanded' => true,
                'multiple' => false,
                'choices' => $options['grilles'],
                'mapped' => true, // Assurez-vous que c'est mappé
                'required' => true,
                'label' => 'Sélectionnez une grille',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FicheGrille::class,
            'grilles' => [],
        ]);
    }
}