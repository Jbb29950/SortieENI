<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReinitialisationMdpFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // A mettre en RepeatedType
        $builder
            ->add('password',RepeatedType::class,[
                'type'=>PasswordType::class,
                'invalid_message'=>"Les deux champs doivent être identiques.",
                'options'=>['attr'=>['class'=>'password-field']],
                'required'=>true,
                'first_options'=>['label'=>'Mot de passe'],
                'second_options'=>['label'=>'Réécrivez votre mot de passe.']


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
