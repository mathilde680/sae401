<?php

namespace App\Controller;

use App\Repository\MatiereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilProfController extends AbstractController
{
    #[Route('/accueil/prof', name: 'app_accueil_prof')]
    public function index(MatiereRepository $MatiereRepository) : response
    {
        $matieres = $MatiereRepository->findAllMatiere();
        $semestre1 = $MatiereRepository->findAllBySemestre('WR1');
        $semestre2 = $MatiereRepository->findAllBySemestre('WR2');
        $semestre3 = $MatiereRepository->findAllBySemestre('WR3');
        $semestre4 = $MatiereRepository->findAllBySemestre('WR4');
        $semestre5 = $MatiereRepository->findAllBySemestre('WR5');
        $semestre6 = $MatiereRepository->findAllBySemestre('WR6');

        return $this->render('accueil_prof/index.html.twig', [
            'matieres' => $matieres,
            'semestre1' => $semestre1,
            'semestre2' => $semestre2,
            'semestre3' => $semestre3,
            'semestre4' => $semestre4,
            'semestre5' => $semestre5,
            'semestre6' => $semestre6,
        ]);
    }
}
