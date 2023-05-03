<?php

namespace App\Controller;

use App\Filtre\FiltreAccueil;
use App\Form\FiltreAccueilType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(private SortieRepository $sortieRepository){}
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $filtre = new FiltreAccueil();
        $form = $this->createForm(FiltreAccueilType::class, $filtre);
        $form->handleRequest($request);
        $participant = $this->getUser();
        $affichables = $this->sortieRepository->trouverAffichable($filtre, $participant);
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'form'=> $form->createView(),
            'affichables'=>$affichables
        ]);
    }

}
