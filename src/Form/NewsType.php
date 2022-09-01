<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\News;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre'
                ]
            ])
            ->add('descriptionShort', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Description courte'
                ]
            ])
            ->add('descriptionLong', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Description longue'
                ]
            ])
            
            // Ajout de la photo
            ->add('news_photo', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
            ])
            //Ajout des catégories
            ->add('categories', EntityType::class,[
                'label' => false,
                'required' => true,
                'class'=>Category::class,
                'choice_label'=> 'label',
                'expanded'=>true,
                'multiple'=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
