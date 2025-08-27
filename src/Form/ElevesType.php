<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElevesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('elevesList', TextareaType::class, [
                'mapped' => false,
                'label' => 'Liste des élèves (1 par ligne)',
                'attr' => [
                    'rows' => 10,
                    'placeholder' => "Alice Dupont\nLéo Martin\nSophie Durand",
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'manage_eleves', // identifiant unique pour ce form
        ]);
    }
}
