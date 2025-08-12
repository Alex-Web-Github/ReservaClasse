<?php

namespace App\Form;

use App\Entity\DateSession;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class DateSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('startTime', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de début',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('endTime', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de fin',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DateSession::class,
        ]);
    }
}
