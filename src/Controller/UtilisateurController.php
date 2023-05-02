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
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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

    public function modifierProfil(Request $request, EntityManagerInterface $entityManager,
                                   SluggerInterface $slugger,
                                   ParticipantRepository $participantRepository
                                   PasswordHasherInterface $passwordHasher): Response
    {
        $user = $this -> getUser();
        $modifierProfilForm = $this -> createForm(UpdateProfileType::class, $user);
        $modifierProfilForm -> handleRequest($request);

        if ($modifierProfilForm -> isSubmitted() && $modifierProfilForm -> isValid()) {
            $pseudo = $user -> getPseudo();
            $mdp = $passwordHasher->hash($modifierProfilForm->get('password')->getData());
            assert($user instanceof Participant);
            $user->setPassword($mdp);
            $photoFile = $modifierProfilForm ->get('photo_profil') -> getData();

            if($photoFile) {

                $originalFileName = pathinfo($photoFile->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFileName= $slugger->slug($originalFileName);
                $newFileName=$safeFileName.'-'.uniqid().'-'.$photoFile->guessExtension();
                try {
                    $photoFile -> move(
                        $this -> getParameter('photo_dir'),
                        $newFileName

                    );
                }
                catch (FileException $e){

                }$user->setPhotoProfil($newFileName);
            }

            if ($pseudo) {
                if ($participantRepository -> findOneBy(['pseudo' => $pseudo])) {
                    $this -> addFlash('fail', 'Pseudo déjà utilisé');
                    return $this -> render('utilisateur/modificationProfil.html.twig', [

                        'modifierProfil' => $modifierProfilForm -> createView(),
                    ]);
                }

            }
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
