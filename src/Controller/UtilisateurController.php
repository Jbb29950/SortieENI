<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use App\Service\Peupler;
use Cassandra\Type\UserType;
use Doctrine\Persistence\ObjectManager;
use http\Client\Curl\User;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function editionProfil(ParticipantRepository $pr): Response
    {
        return $this->render('utilisateur/editionProfil.html.twig', [
            'controller_name' => 'UtilisateurController',
            'participant'=>$pr->findOneBy(['id'=>$this->getUser()->getUserIdentifier()])

        ]);
    }

}
