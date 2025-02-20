<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilEtudiantController extends AbstractController
{
    #[Route('/accueil/etudiant', name: 'app_accueil_etudiant')]
    public function index(): Response
    {
        return $this->render('accueil_etudiant/index.html.twig', [
            'controller_name' => 'AccueilEtudiantController',
        ]);
    }
}
