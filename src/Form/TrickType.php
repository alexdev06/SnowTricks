<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\ImageType;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du trick',
                'attr' => [
                    'placeholder' => 'Nom du trick'
                ]
            ])
            
            ->add('description', TextareaType::class, [
                'label' => 'Description du trick',
                'attr' => [
                    'placeholder' => 'Description du trick'
                ]
            ])

            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie du trick',

            ])

            ->add('imageMain', FileType::class, [
                'label' => 'Image de fond'
            ])

            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Créer le nouveau trick !',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
