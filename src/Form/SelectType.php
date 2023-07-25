<?php

namespace App\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
        $options = [
            '7 derniers jours' => '7',
            '14 derniers jours' => '14',
            '30 derniers jours' => '30',
            '60 derniers jours' => '60',       
        ];

        $type_graphe = [
            'Lineaire' => 'linear',
            'Bar' => 'bar',
            'Lineaire + Bar' => 'mixed',
        ];

        $builder
            ->add('periode', ChoiceType::class, [
                'choices' => $options
                ]
            )
            ->add('visualisation', ChoiceType::class, [
                'choices' => $type_graphe
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
