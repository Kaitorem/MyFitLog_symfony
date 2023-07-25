<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder           
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('sexe', ChoiceType::class, [
                'choices'  => [
                    'Homme' => 'homme',
                    'Femme' => 'femme',
                ],
            ])
            ->add('taille')
            ->add('poids')
            ->add('naissance', TypeDateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ));
            /* ->add('submit', SubmitType::class, array(
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'mt-4'
                ]
            )) */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
