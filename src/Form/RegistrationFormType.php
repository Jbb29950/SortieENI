<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use http\Env\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class RegistrationFormType extends AbstractType
{public function __construct(private CampusRepository $campusRepo)
{}

    public function buildForm(FormBuilderInterface $builder,  array $options):void
    {

        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('telephone',TelType::class)
            ->add('campus',EntityType::class,[
                'choice_label'=>'nom',
                'class'=>Campus::class,
            ])



            #->add('agreeTerms', CheckboxType::class, [
              #  'mapped' => false,
               # 'constraints' => [
                #    new IsTrue([
                 #       'message' => 'You should agree to our terms.',
                  #  ]),
            #],
            #])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir au minimum{{ limit }} caractères',
                        // max length allowed by Symfony for security reasons

                        'max' => 15,
                        'maxMessage'=>'Votre mot de passe ne doit pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
