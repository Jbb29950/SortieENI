<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Time;

class CreerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut',DateType::class,[
                'label'=>'Date de la sortie',
                'widget' => 'single_text',
            ])
            ->add('duree', TimeType::class, [
                'label' =>'Durée (en minutes)'
            ])
            ->add('dateLimiteInscription', DateType::class,[
                'widget' => 'single_text',
                ])
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('lieu', EntityType::class,[
                'class'=>Lieu::class,
                'choice_label' => 'nom',
            ])
            ->add('etat', EntityType::class,[
                'class'=>Etat::class,
                'choice_label'=>'libelle',
                'query_builder'=> function(EntityRepository $er){
                return $er->createQueryBuilder('e')
                    ->andWhere('e.libelle IN (:libelles)')
                    ->setParameter('libelles', ['ouvert', 'en création']);
                },
            ])
            ->add('campus', EntityType::class,[
                'class'=> Campus::class,
                'choice_label' => 'nom',
                'label'=>'Campus'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
