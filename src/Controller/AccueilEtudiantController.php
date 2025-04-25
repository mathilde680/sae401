<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Repository\AlerteRepository;
use App\Repository\EtudiantRepository;
use App\Repository\EvaluationRepository;
use App\Repository\MatiereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilEtudiantController extends AbstractController
{
    #[Route('/etudiant', name: 'app_accueil_etudiant')]
    public function index(EvaluationRepository $evaluationRepository, AlerteRepository $alerteRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Etudiant) {
            throw $this->createAccessDeniedException('Accès réservé aux étudiants.');
        }

        $evaluationsGroupe = $evaluationRepository->findGroupEvaluationsByEtudiant($user);

        $alertes = $alerteRepository->findAll();

        return $this->render('accueil_etudiant/index.html.twig', [
            'controller_name' => 'AccueilEtudiantController',
            'evaluations' => $evaluationsGroupe,
            'alertes' => $alertes,
        ]);
    }
}
