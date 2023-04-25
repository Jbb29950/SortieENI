<?php

namespace Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use App\Security\ParticipantAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator, ParticipantAuthenticator $authenticator,
                             CampusRepository $campusRepo, EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setAdministrateur(false);
            $user->setActif(true);


            $user->setCampus($campusRepo->findOneBy(['nom' => $request->request->get('campus')]));

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData(),


            $entityManager->persist($user)));
            $entityManager->flush();
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'Campus'=>$campusRepo->findAll()
        ]);
    }
}
