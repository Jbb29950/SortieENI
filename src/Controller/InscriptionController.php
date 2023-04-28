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
        if ($sortie->getEtat() != "ouvert" || $sortie->getDateLimiteInscription() < new \DateTime()) {
            $this->addFlash('error', 'La date limite d\'inscription est dépassée ou la sortie est fermée.');
            return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
        }

        // Récupérer l'utilisateur connecté avec le repo
        $user = $this->getUser();
        $participant = $entityManager->getRepository(Participant::class)->findOneBy(['id' => $user->getId()]);

        // Ajouter l'utilisateur à la liste des participants de la sortie
        $sortie->addParticipant($participant);
        $entityManager->persist($participant);
        $entityManager->flush();

        $this->addFlash('success', 'Inscription réussie.');

        return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
    }

    #[Route('/sortie/{id}/desister', name: 'sortie_desister', requirements: ['id' => '\d+'])]

    public function desisterSortie(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté avec le repo
        $user = $this->getUser();
        $participant = $entityManager->getRepository(Participant::class)->findOneBy(['id' => $user->getId()]);

        // Vérifier que l'utilisateur est bien inscrit à la sortie
        if (!$sortie->getParticipants()->contains($participant)) {
            $this->addFlash('error', 'Vous ne pouvez pas vous désister d\'une sortie à laquelle vous n\'êtes pas inscrit.');
            return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
        }

        // Vérifier que la sortie n'a pas encore commencé
        $now = new \DateTime();
        if ($sortie->getDateHeureDebut() <= $now) {
            $this->addFlash('error', 'La sortie a déjà commencé, vous ne pouvez plus vous désister.');
            return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
        }

        // Récupérer le motif d'annulation depuis le formulaire
        $motif = $request->request->get('motif');
        if (empty($motif)) {
            $this->addFlash('error', 'Vous devez fournir un motif d\'annulation.');
            return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
        }

        // Vérifier que la date limite d'inscription n'est pas dépassée
        $dateLimiteInscription = $sortie->getDateLimiteInscription();
        if ($dateLimiteInscription !== null && $dateLimiteInscription <= $now) {
            $this->addFlash('error', 'La date limite d\'inscription est dépassée, vous ne pouvez plus vous désister.');
            return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
        }

        // Ajouter une place disponible si la sortie était complète
        if ($sortie->getParticipants()->count() >= $sortie->getNbInscriptionsMax()) {
            $sortie->setNbInscriptionsMax($sortie->getNbInscriptionsMax() + 1);
        }

        // Désister l'utilisateur de la sortie
        $sortie->removeParticipant($participant);
        $sortie->setMotifAnnulation($motif);
        $sortie->setEtat("fermé");
        $entityManager->flush();

        $this->addFlash('success', 'Votre désistement a été enregistré.');

        return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
    }

}