<?php

namespace App\Controller;

use App\Entity\Grille;
use App\Entity\Matiere;
use App\Form\AjoutGrilleType;
use App\Form\MatiereType;
use App\Repository\GrilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/grille/ajout', name: 'app_grille_ajout')]
    public function ajout_grille(GrilleRepository $grilleRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de matiere
        $grilles = new Grille();

        // Créer le formulaire
        $form = $this->createForm(AjoutGrilleType::class,$grilles);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persister et enregistrer le matiere dans la base de données
            $entityManager->persist($grilles);
            $entityManager->flush();

            // Rediriger vers une autre page (par exemple, la liste des matieres)
            return $this->redirectToRoute('app_accueil_prof');

        }

        return $this->render('grille/ajout.html.twig', [
            'form_grille' => $form->createView(),
            'grilles' => $grilles,
        ]);
    }
}
