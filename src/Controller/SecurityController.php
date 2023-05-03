<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\EmailUserType;
use App\Form\ReinitialisationMdpFormType;
use App\Repository\ParticipantRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Request;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\IsNull;
use function PHPUnit\Framework\isEmpty;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         //if ($this->getUser()) {
            //return $this->redirectToRoute('app_home');
         //}

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {return $this->render('home/home.html.twig', [
        'controller_name' => 'HomeController',
    ]);
    }

    #[Route('/oubli-pass', name:'forgotten_password')]
    public function forgottenPassword(\Symfony\Component\HttpFoundation\Request $request, ParticipantRepository $participantRepository,
    TokenGeneratorInterface $tokenGenerator,
    EntityManagerInterface $entityManager,
    //NotificationEmail $emailMessage,
    ): Response
    {
        $user=new Participant();
        $form=$this->createForm(EmailUserType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted()){

            //on cherche le participant par son email
            $user=$participantRepository->findOneBy(['email'=>$user->getEmail()]);

            //on vérifie si le participant existe
                if(!is_null($user)){

                    //on génère un jeton de reinitialisation
                    $token =$tokenGenerator->generateToken();
                    $user->setResetToken($token);

                    $entityManager->persist($user);
                    $entityManager->flush();

                    return $this->redirectToRoute('reinitialisation_mdp',[
                      'token'=>$token
                    ]);
                }
                $this->addFlash('danger','Un problème est survenu');
                return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig',[
            'requestPassForm'=>$form->createView()

        ]);

    }

    #[Route('/oubli-pass-new-password/{token}', name:'reinitialisation_mdp')]
public function reinitialisationMdp(
       \Symfony\Component\HttpFoundation\Request $request,
        ParticipantRepository $participantRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,string $token,
    ): Response
    {

            $user = $participantRepository->findOneBy(['resetToken' => $token]);
            $form=$this->createForm(ReinitialisationMdpFormType::class,$user);

            $form->handleRequest($request);

            if($form->isSubmitted()&&$form->isValid()){
                $user->setResetToken((''));

                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('succes','Mot de passe changé avec succès');
                return $this->redirectToRoute('app_login');
            }
            return $this->render('security/reset_password.html.twig',[
                'passForm'=>$form->createView()
            ]);

        $this->addFlash('danger','Un problème est survenu');
        return $this->redirectToRoute('app_login');
    }

}
