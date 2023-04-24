<?php


namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreerSortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SortieController extends AbstractController
{

    #[Route('/sortie/creer', name: 'creer_Sortie')]
    public function creerSortie(Request $request): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(CreerSortieType::class, $sortie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer la sortie dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a été créée avec succès.');
            return $this->redirectToRoute('page_d_accueil');
        }

        return $this->render('sortie/creerSortie.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

