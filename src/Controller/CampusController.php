<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    #[Route('/campus', name: 'app_campus')]

    public function ajoutCampus(Request $request,EntityManagerInterface $entityManager): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


                    $entityManager->persist($campus);
                    $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('campus/campus.html.twig', [
            'campusType' => $form->createView(),
        ]);
    }

    }
