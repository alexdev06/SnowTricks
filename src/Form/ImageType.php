<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', FileType::class, [   
                'label' => 'Images illustration',
                'attr' => [
                    'placeholder' => 'Selectionnez une image'
                ],   
                'constraints' => [
                    new ImageConstraint([
                        'mimeTypesMessage' => 'Le fichier n\'est pas une image valide!',
                        'maxSize' => '2048k',
                        'maxSizeMessage' => 'L\'image doit faire moins de 2Mo !',
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
