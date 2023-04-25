<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnulerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie',
                'disabled' => true,
            ])
            ->add('dateHeureDebut', TextType::class, [
                'label' => 'Date et heure de la sortie',
                'disabled' => true,
            ])
            ->add('campus', TextType::class, [
                'label' => 'Campus',
                'disabled' => true,
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'disabled' => true,
            ])
            ->add('motif', ChoiceType::class, [
                'label' => 'Motif de l\'annulation',
                'choices' => [
                    'Météo défavorable' => 'Météo défavorable',
                    'Nombre insuffisant de participants' => 'Nombre insuffisant de participants',
                    'Autre' => 'Autre',
                ],
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Informations sur la sortie',
                'disabled' => true,
            ])
            ->add('id', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
