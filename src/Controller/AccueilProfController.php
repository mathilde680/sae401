<?php

namespace App\Controller;

use App\Repository\CritereRepository;
use App\Repository\GrilleRepository;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilProfController extends AbstractController
{
    #[Route('/accueil/prof', name: 'app_accueil_prof')]
    public function index(Security $security,MatiereRepository $matiereRepository, GrilleRepository $grilleRepository, CritereRepository $critereRepository, NoteRepository $noteRepository) : response
    {
        $user = $security->getUser();
        $profId = $user->getId(); 

        $matieres = $matiereRepository->findAllMatiere();
        $semestre1 = $matiereRepository->findAllBySemestreAndProfesseur('WR1', $profId);
        $semestre2 = $matiereRepository->findAllBySemestreAndProfesseur('WR2', $profId);
        $semestre3 = $matiereRepository->findAllBySemestreAndProfesseur('WR3', $profId);
        $semestre4 = $matiereRepository->findAllBySemestreAndProfesseur('WR4', $profId);
        $semestre5 = $matiereRepository->findAllBySemestreAndProfesseur('WR5', $profId);
        $semestre6 = $matiereRepository->findAllBySemestreAndProfesseur('WR6', $profId);

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
