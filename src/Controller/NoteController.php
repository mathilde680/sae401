<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\FicheNoteCritere;
use App\Entity\Note;
use App\Form\AjoutNoteType;
use App\Repository\CritereRepository;
use App\Repository\EtudiantRepository;
use App\Repository\EvaluationRepository;
use App\Repository\FicheGrilleRepository;
use App\Repository\FicheNoteCritereRepository;
use App\Repository\GrilleRepository;
use App\Repository\GroupeRepository;
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
        $userId = $user->getId();

        $matieres = $matiereRepository->findMatiereAndNoteByEtudiant($user);
        $moyennes = [];

        foreach ($matieres as $matiere) {
            $moyennes[$matiere->getId()] = $noteRepository->findNoteByEtudiantMatiere($userId, $matiere->getId());
        }

        return $this->render('note/noteEtudiant.html.twig', [
            'matieres' => $matieres,
            'moyennes' => $moyennes,
        ]);
    }


    #[Route('/note/detail/{id}', name: 'app_note_detail', requirements: ['id' => '\d+'])]
    public function notes_detail(int $id, NoteRepository $noteRepository, EvaluationRepository $evaluationRepository, FicheNoteCritereRepository $ficheNoteCritereRepository): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();

        $evaluation = $evaluationRepository->find($id);

        $criteres = $ficheNoteCritereRepository->findNoteCriteresByEtudiantAndEvaluation($userId, $id);

        $details = $noteRepository->findAllDetails($evaluation, $user);

        $moyenne = $noteRepository->findNoteByMoyenne($evaluation);
        $noteMin = $noteRepository->findNoteByMin($evaluation);
        $noteMax = $noteRepository->findNoteByMax($evaluation);

        return $this->render('note/noteDetailEtudiant.html.twig', [
            'details' => $details,
            'evaluation' => $evaluation,
            'criteres' => $criteres,
            'moyenne' => $moyenne,
            'noteMin' => $noteMin ? $noteMin->getNote() : 'N/A',
            'noteMax' => $noteMax ? $noteMax->getNote() : 'N/A',
        ]);
    }


    #[Route('/note/{id}', name: 'app_fiche_evaluation', requirements: ['id' => '\d+'])]
    public function evaluation_fiche(int $id, EtudiantRepository $etudiantRepository, NoteRepository $noteRepository, EvaluationRepository $evaluationRepository, FicheGrilleRepository $ficheGrilleRepository, CritereRepository $critereRepository, FicheNoteCritereRepository $ficheNoteCritereRepository): Response
    {
        $evaluation = $evaluationRepository->find($id);
        $idEvaluation = $evaluation->getId();

        if (!$evaluation) {
            throw $this->createNotFoundException('Évaluation non trouvée');
        }
        $matiere  = $evaluation->getMatiere();
        $idMatiere = $matiere->getId();

        // je recupere tout les etudiants liée à la matiere et donc à l'eval
        $etudiants = $etudiantRepository->findEtudiantsByMatiereId($idMatiere);
        usort($etudiants, function ($a, $b) {
            return strcmp($a->getNom(), $b->getNom());
        });

        // Récupérer les notes associées à cette évaluation
        $notes = $evaluation->getNotes()->toArray(); // Convertir la collection en tableau

        $grilleEvaluation = $ficheGrilleRepository->findBy([
            'Evaluation' => $idEvaluation,
        ]);

        foreach ($grilleEvaluation as $ficheGrille) {
            $idGrille = $ficheGrille->getGrille()->getId();
        }

        $criteres = $critereRepository->findBy([
            'Grille' => $idGrille,
        ]);

        $notesParEtudiantEtCritere = [];
        $notesParEtudiant = [];

        foreach ($etudiants as $etudiant) {
            // Stocke les notes critère
            foreach ($criteres as $critere) {
                $notesCritere = $ficheNoteCritereRepository->findBy([
                    'Critere' => $critere,
                    'Etudiant' => $etudiant,
                ]);

                foreach ($notesCritere as $noteCritere) {
                    $notesParEtudiantEtCritere[$etudiant->getId()][$noteCritere->getCritere()->getId()] = $noteCritere;
                }
            }
            $note = $noteRepository->findOneBy([
                'Etudiant' => $etudiant,
                'Evaluation' => $evaluation,
            ]);
            $notesParEtudiant[$etudiant->getId()] = $note;
        }


        return $this->render('note/evaluation.html.twig', [
            'evaluation' => $evaluation,
            'notes' => $notes,
            'criteres' => $criteres,
            'etudiants'=> $etudiants,
            'notesParEtudiantEtCritere' => $notesParEtudiantEtCritere,
            'notesParEtudiant' => $notesParEtudiant,
        ]);
    }

    #[Route('/note/evaluer/{id}', name: 'app_note')]
    public function modif_ajout_note(
        int                        $id, EvaluationRepository $evaluationRepository, Request $request,
        NoteRepository             $noteRepository,
        EntityManagerInterface     $entityManager,
        FicheGrilleRepository      $ficheGrilleRepository,
        CritereRepository          $critereRepository,
        EtudiantRepository         $etudiantRepository,
        FicheNoteCritereRepository $ficheNoteCritereRepository
    ): Response
    {
        // Je recupere l'id de l'eval
        $evaluation = $evaluationRepository->find($id);
        $idEvaluation = $evaluation->getId();

        $idMatiere = $evaluation->getMatiere()->getId();
        if (!$evaluation) {
            throw $this->createNotFoundException('Évaluation non trouvée');
        }

        // je recupere tout les etudiants liée à la matiere et donc à l'eval
        $etudiants = $etudiantRepository->findEtudiantsByMatiereId($idMatiere);
        usort($etudiants, function ($a, $b) {
            return strcmp($a->getNom(), $b->getNom());
        });

        // je recupere la fiche grille associer à l'eval
        $grilleEvaluation = $ficheGrilleRepository->findBy([
            'Evaluation' => $idEvaluation,
        ]);

        // je recupere la grille
        foreach ($grilleEvaluation as $ficheGrille) {
            $idGrille = $ficheGrille->getGrille()->getId();
        }

        // Je recupere les criteres de la grille
        $criteres = $critereRepository->findBy([
            'Grille' => $idGrille,
        ]);

        // récupere les notes qui seraient déja entrée
        $notesExistantes = [];
        $notesEvaluationsExistantes = [];

        foreach ($etudiants as $etudiant) {
            foreach ($criteres as $critere) {
                $noteCritere = $ficheNoteCritereRepository->findOneBy([
                    'Etudiant' => $etudiant,
                    'Critere' => $critere
                ]);

                if ($noteCritere) {
                    $notesExistantes[$etudiant->getId()][$critere->getId()] = $noteCritere->getNote();
                }
            }
            $noteEvaluation = $noteRepository->findOneBy([
                'Evaluation' => $evaluation,
                'Etudiant' => $etudiant,
            ]);
            if ($noteEvaluation) {
                $notesParEtudiant[$etudiant->getId()] = $noteEvaluation;
                $notesEvaluationsExistantes[$etudiant->getId()][$evaluation->getId()] = $noteEvaluation->getCommentaire();
            }
        }

        if ($request->isMethod('POST')) {

            foreach ($etudiants as $etudiant) {
                $noteGlobal = 0;

                foreach ($criteres as $critere) {
                    if ($request->request->has('etu_' . $etudiant->getId() . '_' . $critere->getId())) {
                        $noteCritere = $ficheNoteCritereRepository->findOneBy([
                            'Etudiant' => $etudiant,
                            'Critere' => $critere
                        ]);

                        // Si elle n'existe pas
                        if (!$noteCritere) {
                            $noteCritere = new FicheNoteCritere();
                            $noteCritere->setEtudiant($etudiant);
                            $noteCritere->setCritere($critere);
                            $entityManager->persist($noteCritere);
                        }

                        // Récupération de la note associée dans le formulaire
                        $noteValue = $request->request->get('etu_' . $etudiant->getId() . '_' . $critere->getId());
                        if ($noteValue === '') {
                            $noteValue = null;
                        } else {
                            $noteValue = floatval($noteValue);
                            $noteGlobal += $noteValue;
                        }
                        $noteCritere->setNote($noteValue);

                    }
                }
                $noteEvaluation = $noteRepository->findOneBy([
                    'Evaluation' => $evaluation,
                    'Etudiant' => $etudiant,
                ]);
                if (!$noteEvaluation) {
                    $noteEvaluation = new Note();
                    $noteEvaluation->setEtudiant($etudiant);
                    $noteEvaluation->setEvaluation($evaluation);
                    $entityManager->persist($noteEvaluation);
                }
                $noteEvaluation->setNote($noteGlobal);

                if($request->request->has('etu_' . $etudiant->getId() . '_' . $evaluation->getId())){
                    $commentaireEvalValue = $request->request->get('etu_' . $etudiant->getId() . '_' . $evaluation->getId());
                    if ($commentaireEvalValue === null || $commentaireEvalValue === '') {
                        $commentaireEvalValue = '';
                    }
                    $noteEvaluation->setCommentaire($commentaireEvalValue);
                }
            }

            // Sauvegarde en base
            $entityManager->flush();
            $this->addFlash('success', 'Les notes ont été enregistrées avec succès.');

            return $this->redirectToRoute('app_fiche_evaluation', ['id' => $evaluation->getId()]);
        }


        return $this->render('note/index.html.twig', [
            'evaluation' => $evaluation,
            'notesEvaluationsExistantes' => $notesEvaluationsExistantes,
            //'form_ajout_note' => $form->createView(),
            'etudiants' => $etudiants,
            'criteres' => $criteres,
            'notesParEtudiant' => $notesParEtudiant,
            'notesExistantes' => $notesExistantes,
        ]);
    }
}