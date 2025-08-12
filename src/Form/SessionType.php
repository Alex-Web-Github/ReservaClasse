<?php

namespace App\Form;

use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'Nom de la session',
                'attr' => [
                    'placeholder' => 'Ex: Réunions parents-profs Trimestre 1'
                ]
            ])
            ->add('slotDuration', IntegerType::class, [
                'label' => 'Durée des créneaux (en minutes)',
                'attr' => [
                    'min' => 5,
                    'max' => 120
                ]
            ])
            ->add('slotInterval', IntegerType::class, [
                'label' => 'Intervalle entre les créneaux (en minutes)',
                'attr' => [
                    'min' => 0,
                    'max' => 20
                ]
            ])
            ->add('dates', CollectionType::class, [
                'entry_type' => DateSessionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
