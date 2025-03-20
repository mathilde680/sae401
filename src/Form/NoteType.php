<?php

namespace App\Form;

use App\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'input_note'],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 20,
                        'notInRangeMessage' => 'La note doit être comprise entre {{ min }} et {{ max }}.',
                    ]),
                ]
            ])
            ->add('commentaire', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'input_commentaire'],
                'row_attr' => ['class' => 'commentaire-wrapper'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Veuillez saisir au minimum {{ limit }} caractères',
                        'max' => 600,
                        'maxMessage' => 'Veuillez saisir au maximum {{ limit }} caractères',
                    ]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
