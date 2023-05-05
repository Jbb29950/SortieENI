<?php

namespace App\Controller;



use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CampusType;
use App\Form\UpdateProfileType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;

use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function editionProfil(ParticipantRepository $pr,UserInterface $user): Response
    {
        $user->getUserIdentifier();
        return $this -> render('utilisateur/editionProfil.html.twig', [
            'controller_name' => 'UtilisateurController',
            'participant' => $pr -> findOneBy(['email' => $this -> getUser() -> getUserIdentifier()])

        ]);
    }

    #[Route('/utilisateur/modifier', name: 'modifier_profil')]

    public function modifierProfil(Request $request, EntityManagerInterface $entityManager,
                                   SluggerInterface $slugger,
                                   ParticipantRepository $participantRepository,
                                   UserPasswordHasherInterface $passwordHasher): Response{


        $user = $participantRepository->findOneBy(['email'=>$this->getUser()->getUserIdentifier()]);

        $modifierProfilForm = $this -> createForm(UpdateProfileType::class, $user);
        $modifierProfilForm -> handleRequest($request);

        if ($modifierProfilForm -> isSubmitted() && $modifierProfilForm -> isValid()) {
            $pseudo = $user -> getPseudo();

            assert($user instanceof Participant);

            $photoFile = $modifierProfilForm ->get('photo_profil') -> getData();

            $plainpassword = $modifierProfilForm->get('password')->getData();
            if(!is_null($plainpassword)){
                $mdp = $passwordHasher->hashPassword($user,$plainpassword);
                $user->setPassword($mdp);
            }

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
            $entityManager -> persist($user);
            $entityManager -> flush();

            $this -> addFlash('success', 'Profil modifié avec succès.');

            return $this -> redirectToRoute('app_utilisateur');
        }

        return $this -> render('utilisateur/modificationProfil.html.twig', [

            'modifierProfil' => $modifierProfilForm -> createView(),
        ]);
    }

    #[Route('/utilisateur/inscription/csv', name: 'inscription_csv')]

    public function inscriptionCsv(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('fichier', FileType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('fichier')->getData();

            if ($file) {
                $csv = Reader::createFromPath($file->getPathname(), 'r');
                $csv->setHeaderOffset(0);

                foreach ($csv as $row) {
                    $participant = new Participant();
                    $participant->setNom($row['nom']);
                    $participant->setPrenom($row['prenom']);
                    $participant->setPseudo($row['pseudo']);
                    $participant->setEmail($row['email']);
                    $participant->setTelephone($row['telephone']);
                    $participant->setPassword($passwordHasher->hashPassword($participant, $row['password']));
                    $participant->setRoles([$row['role']]);
                    $participant->setAdministrateur($row['administrateur']);
                    $campus = $entityManager->getRepository(Campus::class)->findOneBy(['id' => $row['campus']]);
                    $participant->setCampus($campus);
                    $participant->setActif(true);
                    $entityManager->persist($participant);
                }

            }

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateurs ajoutés avec succès.');
            return $this->redirectToRoute('app_utilisateur');
        }

        return $this->render('utilisateur/inscriptionCsv.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/participant/{idParticipant}', name:'app_participant')]

    public function afficherProfilParticipant($idParticipant, ParticipantRepository $participantRepository)
    {
        $user= $participantRepository->findOneBy(['id'=> $idParticipant]);
        $user->setTelephone('00000000');
        $user->setEmail('On n\'affiche pas le mail des gens');


        return $this->render('utilisateur/afficherProfil.twig', [
            'user'=> $user
        ]);
    }


    #[Route('/admin/participant', name: 'participant_gestion')]
    public function gestionPartcipant(ParticipantRepository $participantRepository){

        $participants = $participantRepository->findBy([], ['nom'=>'DESC']);


        return $this->render('admin/participant.html.twig',[
            'participants'=>$participants
        ]);
    }
    #[Route('/admin/participant/desactiver/{email}', name: 'participant_gestion_desactiver')]
    public function desactiverParticipant(EntityManagerInterface$entityManager, ParticipantRepository $participantRepository, $email){
        $participant = $participantRepository->findOneBy(['email'=>$email]);
        $participant->setActif(false);
        $entityManager->persist($participant);
        $entityManager->flush();

        $participants = $participantRepository->findBy([], ['nom'=>'DESC']);
        return $this->render('admin/participant.html.twig',[
            'participants'=>$participants
        ]);
    }
    #[Route('/admin/participant/supprimer/{email}', name: 'participant_gestion_supprimer')]
    public function supprimerParticipant(EntityManagerInterface$entityManager, ParticipantRepository $participantRepository, $email){
        $participant = $participantRepository->findOneBy(['email'=>$email]);
        $entityManager->remove($participant);
        $entityManager->flush();

        $participants = $participantRepository->findBy([], ['nom'=>'DESC']);
        return $this->render('admin/participant.html.twig',[
            'participants'=>$participants
        ]);
    }

}
