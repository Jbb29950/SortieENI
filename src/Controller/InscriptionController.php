<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    #[Route('/sortie/inscription/{id}', name: 'inscription_sortie')]
    public function inscriptionSortie(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que la sortie est ouverte et que la date limite d'inscription n'est pas dépassée
        if ($sortie->getEtat() != "ouverte" || $sortie->getDateLimiteInscription() < new \DateTime()) {
            $this->addFlash('error', 'La date limite d\'inscription est dépassée ou la sortie est fermée.');
            return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
        }

        // Récupérer l'utilisateur connecté avec le repo
        $user = $this->getUser();
        $participant = $entityManager->getRepository(Participant::class)->findOneBy(['id' => $user->getId()]);

        // Ajouter l'utilisateur à la liste des participants de la sortie
        $sortie->addParticipant($participant);
        $entityManager->flush();

        $this->addFlash('success', 'Inscription réussie.');

        return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
    }

    #[Route('/sortie/desister/{id}', name: 'desister_sortie')]
    public function desisterSortie(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que la sortie n'a pas encore commencé
        if ($sortie->getDateHeureDebut() <= new \DateTime()) {
            $this->addFlash('error', 'La sortie a déjà commencé.');
            return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
        }

        // Récupérer l'utilisateur connecté avec le repo
        $user = $this->getUser();
        $participant = $entityManager->getRepository(Participant::class)->findOneBy(['id' => $user->getId()]);

        // Retirer l'utilisateur de la liste des participants de la sortie
        $sortie->removeParticipant($participant);
        $entityManager->flush();

        $this->addFlash('success', 'Désistement réussi.');

        return $this->redirectToRoute('modifier_Sortie', ['id' => $sortie->getId()]);
    }
}
