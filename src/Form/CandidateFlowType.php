<?php

namespace App\Form;

use App\Entity\Candidate;
use App\Form\CandidateFlow\DetailsType;
use App\Form\CandidateFlow\DisponibilityType;
use App\Form\CandidateFlow\PersonalType;
use App\Form\CandidateFlow\RGPDType;
use Symfony\Component\Form\Flow\AbstractFlowType;
use Symfony\Component\Form\Flow\FormFlowBuilderInterface;
use Symfony\Component\Form\Flow\Type\NavigatorFlowType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidateFlowType extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder->addStep('personal', PersonalType::class);
        $builder->addStep('disponibility', DisponibilityType::class);
        if ($builder->getData()->hasExperience()) {
            $builder->addStep('details', DetailsType::class);
        }
        $builder->addStep('rgpd', RGPDType::class);

        $builder->add('navigator', NavigatorFlowType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class,
            'step_property_path' => 'currentStep',
        ]);
    }
}
