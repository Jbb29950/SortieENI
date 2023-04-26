<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleFormType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'app_lieu_')]
class LieuController extends AbstractController
{
    #[Route('/modifier', name: ' modifier')]
    public function modifierLieu(EntityManagerInterface $entityManager,VilleRepository $vr,Request $request): Response
    {
        $ville = new Ville();
        $villes = $vr->findAll();
        $creerVilleForm = $this->createForm(VilleFormType::class, $ville);
        $creerVilleForm->handleRequest($request);

        if ($creerVilleForm->isSubmitted()&&$creerVilleForm->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('succes', 'La ville a été créé avec succés');
        }

        return $this->render('ville/gestionville.html.twig', [
            'controller_name' => 'LieuController',
            'villes' => $villes,
            'form'=>$creerVilleForm->createView()
        ]);
    }

}
