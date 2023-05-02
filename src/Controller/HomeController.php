<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(private SortieRepository $sortieRepository){}
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $affichable = $this->sortieRepository->trouverAffichable();
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'affichables'=>$affichable
        ]);
    }

}
