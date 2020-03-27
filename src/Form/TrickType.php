<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\ImageType;
use App\Form\VideoType;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;

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
            ->add('imageMainFile', FileType::class, [
                'mapped' => false,
                'label' => 'Image de fond',
                'attr' => [
                    'placeholder' => 'Ajouter une image de fond'
                ],
                'constraints' => [
                    new ImageConstraint([
                        'mimeTypesMessage' => 'Le fichier n\'est pas une image valide!',
                        'maxSize' => '2048k',
                        'maxSizeMessage' => 'L\'image doit faire moins de 2Mo !'
                    ])
                ]
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
