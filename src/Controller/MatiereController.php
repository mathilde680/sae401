<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\FicheMatiere;
use App\Entity\Matiere;
use App\Form\AjoutEvaluationType;
use App\Form\FicheMatiereType;
use App\Repository\EvaluationRepository;
use App\Repository\FicheMatiereRepository;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MatiereController extends AbstractController
{

    #[Route('/professeur/fiche/{id}', name: 'app_fiche_matiere', requirements: ['id'=>'\d+'])]
    public function matiere_fiche(int $id, MatiereRepository $matiereRepository, EvaluationRepository $evaluationRepository, NoteRepository $noteRepository): Response
    {
        $fiches = $matiereRepository->find($id);
        $evaluations = $evaluationRepository->findBy(['matiere' => $id]);

        $notesStats = [];

        foreach ($evaluations as $evaluation) {
            $evaluationId = $evaluation->getId();

            $notesStats[$evaluationId] = [
                'evaluation' => $evaluation,
                'notesMoy' => $noteRepository->findNoteByMoyenne($evaluationId),
                'notesMin' => $noteRepository->findNoteByMin($evaluationId),
                'notesMax' => $noteRepository->findNoteByMax($evaluationId),
            ];
        }

        return $this->render('matiere/fiche.html.twig', [
            'fiches' => $fiches,
            'evaluations' => $evaluations,
            'notesStats' => $notesStats,
        ]);
    }


    #[Route('/professeur/fiche/{id}/suppression', name: 'app_fiche_suppression', requirements: ['id'=>'\d+'])]
    public function supprimerFiche(int $id, FicheMatiereRepository $ficheMatiereRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer la fiche matière
        $ficheMatiere = $ficheMatiereRepository->find($id);
        $query = $entityManager->createQuery('SELECT f FROM App\Entity\FicheMatiere f WHERE f.id = :id')
            ->setParameter('id', $id);
        $result = $query->getResult();
        dd($result);
        // Supprimer la fiche matière
        $entityManager->remove($ficheMatiere);
        $entityManager->flush();

        // Rediriger vers la liste des fiches après la suppression
        return $this->redirectToRoute('app_accueil_prof');
    }

    #[Route('/professeur/matiere/ajout', name: 'app_matiere_ajout')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Créer une nouvelle instance de matiere
        $ficheMatiere = new FicheMatiere();

        $ficheMatiere->setProfesseur($user);

        // Créer le formulaire
        $form = $this->createForm(FicheMatiereType::class,$ficheMatiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Persister et enregistrer le matiere dans la base de données
            $entityManager->persist($ficheMatiere);
            $entityManager->flush();

            // Rediriger vers une autre page (par exemple, la liste des matieres)
            return $this->redirectToRoute('app_accueil_prof');

        }

        // Afficher le formulaire
        return $this->render('matiere/ajout.html.twig', [
            'form_ficheMatiere' => $form->createView(),
            'ficheMatiere' => $form->getData(),
        ]);
    }

    #[Route('/professeur/matiere/{id}/supprime', name: 'app_matiere_supprime', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprime(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'entité Jeu existante
        $ficheMatiere = $entityManager->getRepository(FicheMatiere::class)->find($id);


        // Supprimer le jeu de la base de données
        $entityManager->remove($ficheMatiere);
        $entityManager->flush();

        // Rediriger vers la liste des jeux après la suppression
        return $this->redirectToRoute('app_accueil_prof');
    }

    #[Route('/professeur/matiere/{id}/modif', name: 'app_matiere_modif', requirements: ['id' => '\d+'])]
    public function modif(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $matiere = $entityManager->getRepository(Matiere::class)->find($id);


        $form = $this->createForm(FicheMatiereType::class, $matiere);
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
            'id' => $id,
            'matiere' => $form->getData(),
        ]);
    }
}
