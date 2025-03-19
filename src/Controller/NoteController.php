<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Note;
use App\Form\AjoutNoteType;
use App\Repository\EvaluationRepository;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NoteController extends AbstractController
{
    #[Route('/note', name: 'app_note_etudiant')]
    public function notes(NoteRepository $noteRepository, MatiereRepository $matiereRepository): Response
    {
        $user = $this->getUser();

        $matieres = $matiereRepository->findMatiereAndNoteByEtudiant($user);

        return $this->render('note/noteEtudiant.html.twig', [
            'matieres' => $matieres,
        ]);
    }

    #[Route('/note/detail/{id}', name: 'app_note_detail', requirements: ['id' => '\d+'])]
    public function notes_detail(NoteRepository $noteRepository, MatiereRepository $matiereRepository): Response
    {
        $user = $this->getUser();

        $matieres = $matiereRepository->findMatiereAndNoteByEtudiant($user);

        return $this->render('note/noteDetailEtudiant.html.twig', [
            'matieres' => $matieres,
        ]);
    }


    #[Route('/note/{id}', name: 'app_fiche_evaluation', requirements: ['id' => '\d+'])]
    public function evaluation_fiche(int $id, EvaluationRepository $evaluationRepository): Response
    {
        $evaluation = $evaluationRepository->find($id);

        if (!$evaluation) {
            throw $this->createNotFoundException('Évaluation non trouvée');
        }

        // Récupérer les notes associées à cette évaluation
        $notes = $evaluation->getNotes()->toArray(); // Convertir la collection en tableau

        // Trier les notes par nom en ordre alphabétique
        usort($notes, function ($a, $b) {
            // Supposant que vous avez une méthode getEleve()->getNom() pour accéder au nom
            return strcmp($a->getEtudiant()->getNom(), $b->getEtudiant()->getNom());
        });

        return $this->render('note/evaluation.html.twig', [
            'evaluation' => $evaluation,
            'notes' => $notes,
        ]);
    }

    #[Route('/note/evaluer/{id}', name: 'app_note')]
    public function modif_ajout_note(
        int $id, EvaluationRepository $evaluationRepository, Request $request,
        NoteRepository $noteRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $evaluation = $evaluationRepository->find($id);

        if (!$evaluation) {
            throw $this->createNotFoundException('Évaluation non trouvée');
        }

        // Récupérer les notes associées à cette évaluation
        $notes = $evaluation->getNotes()->toArray(); // Convertir la collection en tableau

        // Trier les notes par nom en ordre alphabétique
        usort($notes, function($a, $b) {
            // Supposant que vous avez une méthode getEleve()->getNom() pour accéder au nom
            return strcmp($a->getEtudiant()->getNom(), $b->getEtudiant()->getNom());
        });

        //dd($notes);

        $form = $this->createForm(AjoutNoteType::class, ['notes' => $notes]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            foreach ($formData['notes'] as $note) {
                $entityManager->persist($note);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Les notes ont été enregistrées avec succès.');

            return $this->redirectToRoute('app_fiche_evaluation', ['id' => $evaluation->getId()]);

        }

        return $this->render('note/index.html.twig', [
            'evaluation' => $evaluation,
            'notes' => $notes,
            'form_ajout_note' => $form,
        ]);
}

}