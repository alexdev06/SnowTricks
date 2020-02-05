<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ResettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

        ->add('passwordHash', PasswordType::class, [
            'label' => 'Votre nouveau mot de passe',
            'attr' => [
                'placeholder' => 'Nouveau Mot de passe'
            ]
        ])
        ->add('passwordConfirm', PasswordType::class, [
            'label' => 'Confirmez votre nouveau mot de passe',
            'attr' => [
                'placeholder' => 'Confirmation du nouveau mot de passe'
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
