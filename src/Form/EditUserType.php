<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                "constraints" => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une adresse email valide'
                    ])
                ],
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Etudiant' => 'ROLE_ETUDIANT',
                    'Enseignant' => 'ROLE_ENSEIGNANT',
                    'Administrateur' => 'ROLE_SUPER_ADMIN'
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
