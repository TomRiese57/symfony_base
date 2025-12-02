<?php

namespace App\Form\CandidateFlow;

use App\Entity\Candidate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextareaType::class)
            ->add('lastName', TextareaType::class)
            ->add('email', EmailType::class)
            ->add('phone', TextareaType::class, [ 'required' => false ])
            ->add('hasExperience', CheckboxType::class, [ 'required' => false, 'data' => false ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class,
            'inherit_data' => true,
        ]);
    }
}
