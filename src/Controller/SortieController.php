<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnulerSortieType;
use App\Form\CreerSortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Controller\ModifierSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SortieController extends AbstractController
{
    #[Route('/sortie/creer', name: 'creer_Sortie')]
    public function creerSortie(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        $sortie = new Sortie();
        $creerSortieForm = $this->createForm(CreerSortieType::class, $sortie);
        $creerSortieForm->handleRequest($request);

        if ($creerSortieForm->isSubmitted() && $creerSortieForm->isValid()) {


            //Enregistrer l'utilisateur comme organisateur de la sortie
            $sortie->setOrganisateur($this->getUser());

            // Enregistrer la sortie dans la base de données
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a été créée avec succès.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('sortie/creerSortie.html.twig', [
            'creerSortieForm' => $creerSortieForm->createView(),
        ]);
    }

    #[Route ('/sortie/{id}', name: 'afficher_Sortie')]
    public function afficherSortie(Sortie $sortie): Response
    {
        //Sorties cloturées depuis 1 mois pas consultables

        $dateUnMoisAvant = new \DateTime();
        $dateUnMoisAvant->modify('-1 month');

        if ($sortie->getDateHeureDebut() < $dateUnMoisAvant) {
            $this->addFlash('warning', 'Cette sortie est trop ancienne et ne peut plus être consultée.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
        ]);
    }


    #[Route('/sortie/modifier/{id}', name: 'modifier_Sortie')]
    public function modifierSortie(Request $request, Sortie $sortie = null, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si la variable $sortie est null
        if (!$sortie) {
            // Si $sortie est null, rediriger vers une page d'erreur 404
            throw $this->createNotFoundException('La sortie demandée n\'existe pas');
        }

        $modifierSortieForm = $this->createForm(\App\Controller\ModifierSortieType::class, $sortie);
        $modifierSortieForm->handleRequest($request);

        if ($modifierSortieForm->isSubmitted() && $modifierSortieForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Sortie modifiée avec succès.');

            return $this->redirectToRoute('app_home', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/modifierSortie.html.twig', [
            'sortie' => $sortie,
            'modifierSortieForm' => $modifierSortieForm->createView(),
        ]);
    }


    #[Route('/sortie/annuler/{id}', name: 'annuler_Sortie')]
    public function annulerSortie(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, int $id): Response
    {
        $sortie = $sortieRepository->find($id);

        $annulerSortieForm = $this->createForm(AnnulerSortieType::class);

        $annulerSortieForm->handleRequest($request);

        if ($annulerSortieForm->isSubmitted() && $annulerSortieForm->isValid()) {
            $motif = $annulerSortieForm->get('motif')->getData();

            $sortie->setEtat('Annulée');
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a été annulée avec succès.');

            return $this->redirectToRoute('app_home', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/annulerSortie.html.twig', [
            'form' => $annulerSortieForm->createView(),
            'sortie' => $sortie,
        ]);
    }

    public function test(EntityManagerInterface $entityManager)
    {
        $sortiee = new sortie();
        $sortiee->setNom('england');
        $sortiee->setDateHeureDebut(new \DateTime());
        $sortiee->setDuree(30);
        $sortiee->setdatelimiteinscription(new\Datetime("+1"));
        $sortiee->setnbinscription(5);
        $sortiee->setinfosSortie();
        return $this->render('sortie/creerSortie.html.twig',[
        'sortiee' => $sortiee,]);

$entityManager->persist($sortiee);
$entityManager->flush();
    }

}

