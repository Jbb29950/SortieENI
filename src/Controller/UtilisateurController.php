<?php

namespace Controller;

use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
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
    public function index(): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'controller_name' => 'UtilisateurController',
        ]);
    }


}
