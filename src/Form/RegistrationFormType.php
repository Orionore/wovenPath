<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\UniqueUserEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\Regex;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'label' => 'Pseudo',
                'attr' => [
                    'placeholder' => 'Pseudo',
                    'aria-required' => 'true',
                    'aria-describedby' => 'username-help',
                ],
                'label_attr' => [
                    'id' => 'label-username',
                    'class' => 'sr-only',
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Adresse email',
                'attr' => [
                    'placeholder' => 'Email',
                    'aria-required' => 'true',
                    'aria-describedby' => 'email-help',
                ],
                'label_attr' => [
                    'id' => 'label-email',
                    'class' => 'sr-only',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une adresse email']),
                    new Email(['message' => 'Veuillez entrer une adresse email valide']),
                    new UniqueUserEmail()
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'Mot de passe',
                'invalid_message' => 'Les mots de passes doivent être identiques.',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'aria-required' => 'true',
                        'aria-describedby' => 'password-requirements',
                    ],
                    'label_attr' => [
                        'id' => 'label-password',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new Length([
                            'min' => 12,
                            'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                            'max' => 4096,
                        ]),
                        new Regex([
                            'pattern' => '/[A-Z]/',
                            'message' => 'Votre mot de passe doit contenir au moins une lettre majuscule',
                        ]),
                        new Regex([
                            'pattern' => '/[a-z]/',
                            'message' => 'Votre mot de passe doit contenir au moins une lettre minuscule',
                        ]),
                        new Regex([
                            'pattern' => '/[0-9]/',
                            'message' => 'Votre mot de passe doit contenir au moins un chiffre',
                        ]),
                        new Regex([
                            'pattern' => '/[\W]/',
                            'message' => 'Votre mot de passe doit contenir au moins un caractère spécial (ex. @, #, $, etc.)',
                        ]),
                        new NotCompromisedPassword(),
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe',
                    'attr' => [
                        'aria-required' => 'true',
                        'aria-describedby' => 'confirm-password-help',
                    ],
                    'label_attr' => [
                        'id' => 'label-confirm-password',
                    ],
                ],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ["Default","register"],
            'attr' => [
                'aria-label' => 'Formulaire d\'inscription',
                //'novalidate' => 'novalidate',
            ],
        ]);
    }
}