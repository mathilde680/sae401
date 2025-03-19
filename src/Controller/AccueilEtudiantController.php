<?php

namespace App\Controller;

use App\Repository\EtudiantRepository;
use App\Repository\EvaluationRepository;
use App\Repository\MatiereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilEtudiantController extends AbstractController
{
    #[Route('/accueil/etudiant', name: 'app_accueil_etudiant')]
    public function index(EvaluationRepository $evaluationRepository): Response
    {
        $etudiant = $this->getUser();
        $evaluationsGroupe = $evaluationRepository->findGroupEvaluationsByEtudiant($etudiant);

        return $this->render('accueil_etudiant/index.html.twig', [
            'controller_name' => 'AccueilEtudiantController',
            'evaluations' => $evaluationsGroupe,
        ]);
    }
}
