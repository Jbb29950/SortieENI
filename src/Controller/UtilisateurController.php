<?php

namespace App\Controller;



use App\Entity\Participant;
use App\Form\UpdateProfileType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function editionProfil(ParticipantRepository $pr): Response
    {
        return $this -> render('utilisateur/editionProfil.html.twig', [
            'controller_name' => 'UtilisateurController',
            'participant' => $pr -> findOneBy(['id' => $this -> getUser() -> getUserIdentifier()])

        ]);
    }

    #[Route('/utilisateur/modifier', name: 'modifier_profil')]
    public function modifierProfil(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        $user = $this -> getUser();
        $modifierProfilForm = $this -> createForm(UpdateProfileType::class, $user);
        $modifierProfilForm -> handleRequest($request);
        dump($user);
        if ($modifierProfilForm -> isSubmitted() && $modifierProfilForm -> isValid()) {
            $pseudo = $user -> getPseudo();


            $photoFile = $modifierProfilForm -> get('photo_profil') -> getData();
            $ficherPhoto = md5(uniqid()) . '.' . $photoFile -> guessExtension();
            $photoFile -> move(
                $this -> getParameter('photo_dir'),
                $ficherPhoto
            );

            if ($pseudo) {
                if ($participantRepository -> findOneBy(['pseudo' => $pseudo])) {
                    $this -> addFlash('fail', 'Pseudo déjà utilisé');
                    return $this -> render('utilisateur/modificationProfil.html.twig', [

                        'modifierProfil' => $modifierProfilForm -> createView(),
                    ]);
                }

            }
            dd($user);
            $entityManager -> persist($user);
            $entityManager -> flush();

            $this -> addFlash('success', 'Profil modifié avec succès.');
            dump($user);
            return $this -> redirectToRoute('app_utilisateur');
        }

        return $this -> render('utilisateur/modificationProfil.html.twig', [

            'modifierProfil' => $modifierProfilForm -> createView(),
        ]);
    }
}
