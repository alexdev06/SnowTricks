<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;


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
            ->add('loginName', TextType::class,[
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
            ->add('avatarFile', FileType::class, [
                'required' => false,
                'label' => 'Votre photo de profile',
                'mapped' => false,
                'constraints' => [
                    new ImageConstraint([
                        'mimeTypesMessage' => 'Le fichier n\'est pas une image valide!',
                        'maxSize' => '2048k',
                        'maxSizeMessage' => 'L\'image doit faire moins de 2Mo !',
                    ])
                ]
            ])
            ->add('consentCheck', CheckboxType::class, [
                'label' => 'En soumettant ce formulaire, j\'accepte que mes informations soient utilisées dans le cadre de la gestion des utilisateurs du site SnowTricks.',
                'mapped' => false
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
