<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Form\MatiereType;
use App\Repository\MatiereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MatiereController extends AbstractController
{

    #[Route('/fiche/{id}', name: 'app_fiche_matiere', requirements: ['id'=>'\d+'])]
    public function matiere_fiche(int $id, MatiereRepository $matiereRepository): Response
    {
        $fiches = $matiereRepository->findBy(['id' => $id]);

        return $this->render('matiere/fiche.html.twig', [
            'fiches' => $fiches,
        ]);
    }

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

            // Rediriger vers une autre page (par exemple, la liste des matieres)
            return $this->redirectToRoute('app_accueil_prof');

        }

        // Afficher le formulaire
        return $this->render('matiere/ajout.html.twig', [
            'form_matiere' => $form->createView(),
            'matiere' => $form->getData(),
        ]);
    }

    #[Route('/matiere/{id}/supprime', name: 'app_matiere_supprime', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprime(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'entité Jeu existante
        $matiere = $entityManager->getRepository(Matiere::class)->find($id);

        // Vérifier si le jeu existe
        if (!$matiere) {
            throw $this->createNotFoundException('Matière Inexistant');
        }

        // Supprimer le jeu de la base de données
        $entityManager->remove($matiere);
        $entityManager->flush();

        // Rediriger vers la liste des jeux après la suppression
        return $this->redirectToRoute('app_accueil_prof');
    }

    #[Route('/matiere/{id}/modif', name: 'app_matiere_modif', requirements: ['id' => '\d+'])]
    public function modif(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $matiere = $entityManager->getRepository(Matiere::class)->find($id);
        if (!$matiere) {
            throw $this->createNotFoundException('Matière Inexistant');
        }

        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Persister et enregistrer les modifications dans la base de données
            $entityManager->persist($matiere);
            $entityManager->flush();

            // Rediriger vers la fiche du Jeu
            return $this->redirectToRoute('app_accueil_prof', ['code' => $matiere->getId()]);
        }

        //affiche le formulaire
        return $this->render('matiere/modif.html.twig', [
            'form_matiere' => $form->createView(),
            //'jeu' => $jeu,
            'id' => $id,
            'matiere' => $form->getData(),
        ]);
    }
}
