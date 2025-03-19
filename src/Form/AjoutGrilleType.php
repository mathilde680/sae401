<?php

namespace App\Form;

use App\Entity\Grille;
use App\Entity\Professeur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AjoutGrilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la grille',
                'attr' => ['placeholder' => 'Nom'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom pour la grille.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Veuillez saisir au minimum {{ limit }} caractères.',
                        'max' => 255,
                        'maxMessage' => 'Veuillez saisir au maximum {{ limit }} caractères.',
                    ]),
                ],
            ])


            ->add('professeur', EntityType::class, [
                'class' => Professeur::class,
                'choice_label' => 'id',
                'disabled' => true,
            ])
            ->add('criteres', CollectionType::class, [
                'entry_type' => AjoutCritereType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grille::class,
        ]);
    }
}
