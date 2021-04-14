<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Exercice;
use App\Entity\Formation;
use App\Repository\ExerciceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Undocumented function
    
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     * @method User|null getUser()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('title', null, [
                'label' => 'Titre'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title'
            ])
            ->add('description')
            ->add('exercices', EntityType::class, [
                'class' => Exercice::class,
                'query_builder' => function (ExerciceRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.author = '.$this->security->getUser()->getId()) //on ne fais pas avec l'id car getuser connait que le username utile pour l'authentifiction, pas l'id.
                        ->orderBy('e.title', 'ASC');
                },
                'choice_label' => 'title',
                'expanded'  => true,
                'multiple'  => true,
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
