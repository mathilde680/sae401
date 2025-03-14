<?php

namespace App\Controller;

use App\Repository\GrilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GrilleController extends AbstractController
{
    #[Route('/grille', name: 'app_grille')]
    public function index(GrilleRepository $grilleRepository): Response
    {
        $grilles = $grilleRepository->findAll();

        return $this->render('grille/index.html.twig', [
            'grilles' => $grilles,
        ]);
    }
}
