<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Note;
use App\Form\AjoutNoteType;
use App\Repository\EvaluationRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NoteController extends AbstractController
{
    #[Route('/note/{id}', name: 'app_note')]
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

        $form = $this->createForm(AjoutNoteType::class, $notes);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($notes as $note) {
                $note->setEtudiant($evaluation->getEtudiant());
                $entityManager->persist($note);
                $entityManager->flush();
            }

        }

        return $this->render('note/index.html.twig', [
            'evaluation' => $evaluation,
            'notes' => $notes,
            'form_ajout_note' => $form->createView(),
        ]);
}
}