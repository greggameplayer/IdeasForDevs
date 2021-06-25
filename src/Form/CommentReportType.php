<?php

namespace App\Form;

use App\Entity\ReportComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class CommentReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reason', ChoiceType::class, [
                'choices' => [
                    'Insultes' => 'Insultes',
                    'Racisme' => 'Racisme',
                    'Contenu inapproprié' => 'Contenu inapproprié'
                ],
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
                "attr" => [
                    "class" => "form-control mb-2"
                ],
                'label'=>'Raison :'
            ])
            ->add('description', TextareaType::class, [
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0 mt-2"
                ],
                "attr" => [
                    "class" => "form-control mb-2"
                ],
                'label'=>'Description :',
                'constraints' => [
                    new Length(['min' => 3, 'max' => 499, 'minMessage' => 'La description doit être d\'au moins {{ limit }} caractères !', 'maxMessage' => 'La description doit contenir au maximum {{ limit }} caractères !'])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReportComment::class,
        ]);
    }
}
