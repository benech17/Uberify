<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\ExerciceFiltre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class ExerciceFiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minDifficulte', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Difficulte Minimale'
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'label'=>false
            ])
            ->add('appreciation', ChoiceType::class, [
                'choices'=> [
                    'Du plus apprecié au moins apprecié' =>0,
                    'Du moins apprecié au plus apprecié' =>1,
                ],
                'label'=>false

            ])
            //button submit aded on the html index
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExerciceFiltre::class,
            'method' => 'get',
            'csrf_protection' => false,
        ]);
    }

    //permet de vider le champ get, il sera propre
    public function getBlockPrefix()
    {
        return '';
    }
}
