<?php

namespace App\Form;

use App\Entity\Apply;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, [
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
                "attr" => [
                    "class" => "form-control"
                ],
                "required" => false,
                'label'=>'Description candidature :'
            ])


            ->add("Sauvegarder", SubmitType::class, [
                "attr" => [
                    "class" => "btn btn-primary w-100"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Apply::class,
        ]);
    }
}
