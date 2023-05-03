<?php

namespace App\Form;

use App\Entity\Campus;
use App\Filtre\FiltreAccueil;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreAccueilType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class,[
                'choice_label' => 'nom',
                'label' => false,
                'required' => false,
                'class'=> Campus::class,
            ])
            ->add('contient', TextType::class,[
                'label' => false,
                'required' => false,
                'attr'=>[
                    'placeholder'=>'Contient'
                ]
            ])
            ->add('debutInterval', DateType::class,[
                'widget' => 'single_text',
                'label' => 'Entre',
                'required' => false,
                'placeholder' => new \DateTime('1950'),
                'invalid_message'=> false

            ])
            ->add('finInterval', DateType::class,[
                'widget' => 'single_text',
                'label' => 'et',
                'required' => false,
                'placeholder' => new \DateTime('2100'),
                'invalid_message'=> false

            ])
            ->add('organisateur', CheckboxType::class,[
                'label' => 'Sorties dont je suis L\'organisateur/trice',
                'required' => false,
            ])
            ->add('inscrit', CheckboxType::class,[
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
            ])
            ->add('nonInscrit', CheckboxType::class,[
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
            ])
            ->add('passe', CheckboxType::class,[
                'label' => 'Sorties Passées',
                'required' => false,
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FiltreAccueil::class,
            'csrf_protection'=>false
        ]);
    }
    public function getBlockPrefix(): string
    {
        return '';
    }
}

