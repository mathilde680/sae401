<?php

namespace App\Form;

use App\Entity\Critere;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AjoutCritereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du critere',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un critère',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Veuillez saisir au minimum {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Veuillez saisir au maximum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('note', IntegerType::class, [
                'label' => 'Note',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une note',
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Veuillez saisir au minimum {{ limit }} caractères',
                        'max' => 2,
                        'maxMessage' => 'Veuillez saisir au maximum {{ limit }} caractères',

                    ]),
                ],
            ])

            ->add('commentaire', TextType::class, [
                'label' => 'Commentaire',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un commentaire',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Veuillez saisir au minimum {{ limit }} caractères',
                        'max' => 600,
                        'maxMessage' => 'Veuillez saisir au maximum {{ limit }} caractères',

                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Critere::class,
        ]);
    }
}
