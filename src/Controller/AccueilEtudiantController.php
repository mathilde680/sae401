<?php

namespace App\Controller;

use App\Repository\MatiereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilEtudiantController extends AbstractController
{
    #[Route('/accueil/etudiant', name: 'app_accueil_etudiant')]
    public function index(MatiereRepository $matiereRepository): Response
    {
        $matieres = $matiereRepository->findAllMatiere();

        return $this->render('accueil_etudiant/etudiant.html.twig', [
            'controller_name' => 'AccueilEtudiantController',
            'matieres' => $matieres,
        ]);
    }
}
