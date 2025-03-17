<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ReclamationController extends AbstractController
{


    #[Route('/etudiant/reclamation', name: 'app_reclamation_etudiant')]
    #[IsGranted('ROLE_ETUDIANT')]
    public function index(): Response
    {
        return $this->render('reclamation/etudiant.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }

    #[Route('/prof/reclamation', name: 'app_reclamation_prof')]
    #[IsGranted('ROLE_PROFESSEUR')]
    public function profView(): Response
    {

        return $this->render('reclamation/prof.html.twig');
//        $reclamations = $this->reclamationRepository->findAll();
//
//        return $this->render('reclamation/prof.html.twig', [
//            'reclamations' => $reclamations
//        ]);
    }
}
