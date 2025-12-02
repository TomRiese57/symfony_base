<?php

namespace App\Form\CandidateFlow;

use App\Entity\Candidate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisponibilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('Disponible_immediatement', CheckboxType::class, [ 'mapped' => false, 'required' => false ]);
        dd($builder);
        //$bool = $builder->getData()->isDisponibleImmediatement() ? false : true;
        $builder->add('availabilityDate', DateType::class, [ 'required' => false ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class,
            'inherit_data' => true,
        ]);
    }
}
