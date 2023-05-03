<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Filtre\FiltreAccueil;
use App\Form\AnnulerSortieType;
use App\Form\CreerSortieType;
use App\Form\FiltreAccueilType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Form\ModifierSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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

        $modifierSortieForm = $this->createForm(\App\Form\ModifierSortieType::class, $sortie);
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


    #[Route('/sortie/supprimer/{id}', name: 'supprimer_Sortie')]
    public function supprimerSortie(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, int $id, EtatRepository $etatRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        if (is_null($sortie));
        {
            $this->redirectToRoute('app_home');
        }

        // Vérifier que l'utilisateur connecté est l'organisateur de la sortie
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') && !$sortie->getOrganisateur()->getId() == $user->getId())  {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cette sortie.');
            return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
        }

        // Vérifier que la sortie n'a pas encore commencé
        $now = new \DateTime();
        if ($sortie->getDateHeureDebut() <= $now) {
            $this->addFlash('error', 'La sortie a déjà commencé, vous ne pouvez plus la supprimer.');
            return $this->redirectToRoute('afficher_Sortie', ['id' => $sortie->getId()]);
        }

        // Créer un formulaire pour confirmer la suppression avec un champ "motif"
        $form = $this->createFormBuilder()
            ->add('motif', TextareaType::class, [
                'label' => 'Motif de la suppression',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Veuillez saisir un motif pour la suppression de la sortie',
                ],
            ])
            ->add('supprimer', SubmitType::class, [
                'label' => 'Supprimer',
                'attr' => [
                    'class' => 'btn btn-danger',
                    'onclick' => "return confirm('Êtes-vous sûr de vouloir supprimer cette sortie?')",
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //MàJ du statut de la sortie
            $sortie->setEtat($etatRepository->findOneBy(['libelle'=>'Annulé']));
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a été supprimée avec succès.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('sortie/annulerSortie.html.twig', [
            'form' => $form->createView(),
            'sortie' => $sortie,
        ]);
    }
    #[Route('/', name: 'app_home')]
    public function index(Request $request, SortieRepository $sortieRepository): Response
    {
        $filtre = new FiltreAccueil();
        $form = $this->createForm(FiltreAccueilType::class, $filtre);
        $form->handleRequest($request);
        $participant = $this->getUser();
        $affichables = $sortieRepository->trouverAffichable($filtre, $participant);
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'form'=> $form->createView(),
            'affichables'=>$affichables
        ]);
    }


}


