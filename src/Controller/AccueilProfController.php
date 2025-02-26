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
        $matieres = $MatiereRepository->findAll();

        return $this->render('accueil_prof/index.html.twig', [
            'matieres' => $matieres,
        ]);
    }
}
