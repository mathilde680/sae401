<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Form\MatiereType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MatiereController extends AbstractController
{

    #[Route('/matiere/ajout', name: 'app_matiere_ajout')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de matiere
        $matiere = new Matiere();

        // Créer le formulaire
        $form = $this->createForm(MatiereType::class,$matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persister et enregistrer le matiere dans la base de données
            $entityManager->persist($matiere);
            $entityManager->flush();

            // Rediriger vers une autre page (par exemple, la liste des matierex)
            return $this->redirectToRoute('app_accueil_prof');

        }

        // Afficher le formulaire
        return $this->render('matiere/ajout.html.twig', [
            'form_matiere' => $form->createView(),
            'matiere' => $form->getData(),
        ]);
    }
}
