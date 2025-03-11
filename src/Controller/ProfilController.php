<?php

namespace App\Controller;

use App\Repository\EtudiantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(EtudiantRepository $etudiantRepository): Response
    {
        $etudiant = $this->getUser();

        return $this->render('profil/index.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }
}
