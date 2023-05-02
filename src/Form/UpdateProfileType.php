<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UpdateProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            #->add('roles')

            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('pseudo')
            ->add('photo_profil',FileType::class,[
                'label'=>'Ajouter votre photo',
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new File(['maxSize'=>'2048k',
                      #  'mimeTypes' =>[
                      #      'application/jpeg',
                      #      'application/jpg'

                      #  ],
                      #  'mimeTypesMessage'=>'Veuillez insérer un format valide',
                    ])
                ],
        ])

            #->add('sortie')
            #->add('password')
            #->add('administrateur')
            #->add('actif')

            #->add('campus')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
