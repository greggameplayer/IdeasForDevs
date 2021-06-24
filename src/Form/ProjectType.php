<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\Project;
use App\Entity\Skill;
use App\Repository\SkillRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('repo', TextType::class, [
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
                "attr" => [
                    "class" => "form-control"
                ],
                "required" => false,
                'label'=>'Lien du repository :'
            ])

            ->add('name', TextType::class, [
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
                "attr" => [
                    "class" => "form-control"
                ],
                'label'=>'Nom du projet :'
            ])

            ->add('description', TextType::class, [
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
                "attr" => [
                    "class" => "form-control"
                ],
                'label'=>'Description du projet :'
            ])



            ->add('skillsNeeded', EntityType::class, [
                'class' => Skill::class,
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
                "attr" => [
                    "class" => "form-label"
                ],
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                "required" => false,
                'label'=>'Compétences recherchées :'
            ])

            ->add('jobNeeded', EntityType::class, [
                'class' => Job::class,
                "label_attr" => [
                    "class" => "h3 ml-4 mb-0"
                ],
                "attr" => [
                    "class" => "form-control"
                ],
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                "required" => false,
                'label'=>'Métiers recherchés :'
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
            'data_class' => Project::class,
        ]);
    }
}
