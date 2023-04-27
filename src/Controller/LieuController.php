<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\VilleFormType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'app_lieu_')]
class LieuController extends AbstractController
{
    #[Route('/modifierVille', name: ' modifier_Ville')]
    public function modifierVille(EntityManagerInterface $entityManager,VilleRepository $vr,Request $request): Response
    {
        $ville = new Ville();
        $creerVilleForm = $this->createForm(VilleFormType::class, $ville);
        $creerVilleForm->handleRequest($request);

        if ($creerVilleForm->isSubmitted()&&$creerVilleForm->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('succes', 'La ville a été créé avec succés');
        }
        $villes = $vr->findAll();

        return $this->render('ville/gestionville.html.twig', [
            'controller_name' => 'LieuController',
            'villes' => $villes,
            'form'=>$creerVilleForm->createView()
        ]);
    }

    #[Route('/modifierLieu', name: ' modifier_lieu')]
    public function modifierLieu(EntityManagerInterface $entityManager,LieuRepository $repo,Request $request): Response
    {
        $lieu = new Lieu();
        $creerLieuForm = $this->createForm(Lieu::class,$lieu);
        $creerLieuForm->handleRequest($request);

        if ($creerLieuForm->isSubmitted()&&$creerLieuForm->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('succes', 'Le lieu a été créé avec succés');
        }
        $lieux = $repo->findAll();

        return $this->render('lieu/index.html.twig', [
            'controller_name' => 'LieuController',
            'villes' => $lieux,
            'form'=>$creerLieuForm->createView()
        ]);
    }
}
