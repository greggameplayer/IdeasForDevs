<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar', AvatarType::class, [
                'mapped' => false,
                'label'=>'Avatar :',
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
            ])
            ->add('firstname', TextType::class, [
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
                "attr" => [
                    "class" => "form-control"
                ],
                'label'=>'Prénom :'
            ])
            ->add('lastname', TextType::class, [
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
                "attr" => [
                    "class" => "form-control"
                ],
                'label'=>'Nom :'
            ])
            ->add('birthDate', DateType::class, [
                "label" => "Date de naissance :",
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
                "attr" => [
                    "class" => "form-control"
                ],
                "widget" => "single_text"
            ])
            ->add('email', EmailType::class, [
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
                "attr" => [
                    "class" => "form-control mb-2"
                ],
                'label'=>'Email :'
            ])
            ->add('password', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'Les mot de passe doivent être identique.',
                'mapped' => false,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        "class" => "form-control"
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'S\'il vous plaît entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe devrait faire au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe :', 'label_attr' => ["class" => "h3 ml-4 mb-0"]],
                'second_options' => ['label' => 'Confirmer le mot de passe :', 'label_attr' => ["class" => "h3 ml-4 mb-0"]],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
