<?php

namespace App\Controller;

use App\Repository\CritereRepository;
use App\Repository\FicheMatiereRepository;
use App\Repository\GrilleRepository;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilProfController extends AbstractController
{
    #[Route('/professeur', name: 'app_accueil_prof')]
    public function index(Security $security, FicheMatiereRepository $ficheMatiereRepository, GrilleRepository $grilleRepository, CritereRepository $critereRepository, NoteRepository $noteRepository) : response
    {
        $user = $security->getUser();
        $profId = $user->getId(); 

        //$semestre1 = $ficheMatiereRepository->findAllBySemestreAndProfesseur('WR1', $profId);
        $semestre2 = $ficheMatiereRepository->findAllBySemestreAndProfesseur('WR2', $profId);
       // $semestre3 = $ficheMatiereRepository->findAllBySemestreAndProfesseur('WR3', $profId);
        $semestre4 = $ficheMatiereRepository->findAllBySemestreAndProfesseur('WR4', $profId);
        //$semestre5 = $ficheMatiereRepository->findAllBySemestreAndProfesseur('WR5', $profId);
        $semestre6 = $ficheMatiereRepository->findAllBySemestreAndProfesseur('WR6', $profId);

        $grilles = $grilleRepository->findAllByProfesseur($profId);

        //$criteres = $critereRepository->findCriteresByGrille($grilleId);
        $criteres = $critereRepository->findAll();


        return $this->render('accueil_prof/index.html.twig', [
            //'semestre1' => $semestre1,
            'semestre2' => $semestre2,
            //'semestre3' => $semestre3,
            'semestre4' => $semestre4,
            //'semestre5' => $semestre5,
            'semestre6' => $semestre6,
            'grilles' => $grilles,
            'criteres' => $criteres,
        ]);
    }
}
