<?php

namespace App\Form;

use App\Entity\Category;
use App\Models\SearchClass;
use Doctrine\DBAL\Types\ArrayType;
use SearchNews;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Notifier\Exception\LengthException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', TextType::class, [
                'label' => false, 
                'attr' => [
                    'placeholder' => 'Votre recherche'
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre recherche doit faire au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('categories', EntityType::class, [
                'label' => false,
                'placeholder' =>'Catégories:',
                'required' => false,
                'class'=>Category::class,
                'choice_label'=> 'label',
                'expanded'=>false,
                'multiple'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchNews::class,
        ]);
    }
}