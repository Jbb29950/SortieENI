<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifierSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('dateHeureDebut', DateType::class, [
                'label' => 'Date et heure de début'
            ])
            ->add('duree', TimeType::class, [
                'label' =>'Durée (en minutes)'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription'
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de places'
            ])
            ->add('infosSortie', TextType::class, [
                'label' => 'Description et infos'
            ])
            ->add('etat', EntityType::class, [
                'label' => 'Etat',
                'class'=>Etat::class,
                'choice_label'=>'libelle'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
