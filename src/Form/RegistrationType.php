<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class,[
                'label' => 'Votre prénom',
                'attr' =>
                [
                    'placeholder' => 'Prénom'
                ]
            ])
            ->add('lastName', TextType::class,[
                'label' => 'Votre nom',
                'attr' =>
                [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('userLogin', TextType::class,[
                'label' => 'Votre nom d\'utilisateur',
                'attr' =>
                [
                    'placeholder' => 'Nom d\'utilisateur'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'attr' => [
                    'placeholder' => 'Email'
                ]
            ])
            ->add('passwordHash', PasswordType::class, [
                'label' => 'Votre mot de passe',
                'attr' => [
                    'placeholder' => 'Mot de passe'
                ]
            ])
            ->add('passwordConfirm', PasswordType::class, [
                'label' => 'Confirmez votre mot de passe',
                'attr' => [
                    'placeholder' => 'Confirmation du mot de passe'
                ]
            ])
            ->add('avatar', FileType::class, [
                'required' => false,
                'label' => 'Votre photo de profile'
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
