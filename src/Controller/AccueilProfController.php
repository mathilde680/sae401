<?php

namespace App\Controller;

use App\Repository\CritereRepository;
use App\Repository\GrilleRepository;
use App\Repository\MatiereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilProfController extends AbstractController
{
    #[Route('/accueil/prof', name: 'app_accueil_prof')]
    public function index(Security $security,MatiereRepository $MatiereRepository, GrilleRepository $grilleRepository, CritereRepository $critereRepository) : response
    {
        $user = $security->getUser();
        $profId = $user->getId(); // Supposons que l'utilisateur a un getId()

        $matieres = $MatiereRepository->findAllMatiereByProfesseur($profId);
        $semestre1 = $MatiereRepository->findAllBySemestreAndProfesseur('WR1', $profId);
        $semestre2 = $MatiereRepository->findAllBySemestreAndProfesseur('WR2', $profId);
        $semestre3 = $MatiereRepository->findAllBySemestreAndProfesseur('WR3', $profId);
        $semestre4 = $MatiereRepository->findAllBySemestreAndProfesseur('WR4', $profId);
        $semestre5 = $MatiereRepository->findAllBySemestreAndProfesseur('WR5', $profId);
        $semestre6 = $MatiereRepository->findAllBySemestreAndProfesseur('WR6', $profId);

        $grilles = $grilleRepository->findAll();

        //$criteres = $critereRepository->findCriteresByGrille($grilleId);
        $criteres = $critereRepository->findAll();


        return $this->render('accueil_prof/index.html.twig', [
            'matieres' => $matieres,
            'semestre1' => $semestre1,
            'semestre2' => $semestre2,
            'semestre3' => $semestre3,
            'semestre4' => $semestre4,
            'semestre5' => $semestre5,
            'semestre6' => $semestre6,
            'grilles' => $grilles,
            'criteres' => $criteres,
        ]);
    }
}
