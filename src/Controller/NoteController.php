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

        $matieres = $matiereRepository->findMatiereAndNoteByEtudiant($user);

        return $this->render('note/noteEtudiant.html.twig', [
            'matieres' => $matieres,
        ]);
    }

    #[Route('/note/detail/{id}', name: 'app_note_detail', requirements: ['id' => '\d+'])]
    public function notes_detail(int $id, NoteRepository $noteRepository, EvaluationRepository $evaluationRepository): Response
    {
        $user = $this->getUser();
        $evaluation = $evaluationRepository->find($id);

        $details = $noteRepository->findAllDetails($evaluation, $user);


        return $this->render('note/noteDetailEtudiant.html.twig', [
            'details' => $details,
            'evaluation' => $evaluation,
        ]);
    }


    #[Route('/note/{id}', name: 'app_fiche_evaluation', requirements: ['id' => '\d+'])]
    public function evaluation_fiche(int $id, EvaluationRepository $evaluationRepository, FicheGrilleRepository $ficheGrilleRepository, CritereRepository $critereRepository, FicheNoteCritereRepository $ficheNoteCritereRepository): Response
    {
        $evaluation = $evaluationRepository->find($id);
        $idEvaluation = $evaluation->getId();

        if (!$evaluation) {
            throw $this->createNotFoundException('Évaluation non trouvée');
        }

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

        // Trier les notes par nom en ordre alphabétique
        usort($notes, function ($a, $b) {
            // Supposant que vous avez une méthode getEleve()->getNom() pour accéder au nom
            return strcmp($a->getEtudiant()->getNom(), $b->getEtudiant()->getNom());
        });

        return $this->render('note/evaluation.html.twig', [
            'evaluation' => $evaluation,
            'notes' => $notes,
            'criteres' => $criteres,
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


        // Récupérer les notes associées à cette évaluation
        //$notes = $evaluation->getNotes()->toArray(); // Convertir la collection en tableau

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

        // j'instancie FicheNoteCritere (la table qui stock les note par critere des etudiants)
        //$noteCritere = new FicheNoteCritere();

        // Trier les notes par nom en ordre alphabétique
//        usort($noteCritere, function ($a, $b) {
//            // Supposant que vous avez une méthode getEleve()->getNom() pour accéder au nom
//            return strcmp($a->getEtudiant()->getNom(), $b->getEtudiant()->getNom());
//        });

        // Préparez les données pour le formulaire
        // Partie modifiée de votre contrôleur
        $formData = ['note' => []];

        foreach ($etudiants as $etudiant) {
            foreach ($criteres as $critere) {
                $noteCritere = new FicheNoteCritere();
                $noteCritere->setEtudiant($etudiant);
                $noteCritere->setCritere($critere);

                // $formData['note'][$etudiant->getId()][$critere->getId()] = $noteCritere;
            }
        }

        //$form = $this->createForm(AjoutNoteType::class, $formData);

        // Si vous avez déjà des notes, utilisez-les
//        if (!empty($noteCritere)) {
//            $formData['note'] = $noteCritere;
//        } else {
//            // Sinon, créez de nouvelles notes pour chaque étudiant
//            $noteCollection = [];
//            foreach ($etudiants as $etudiant) {
//                $ficheNote = new FicheNoteCritere();
//                $ficheNote->setEtudiant($etudiant);
//                $noteCollection[] = $ficheNote;
//            }
//            $formData['note'] = $noteCollection;
//        }

        //$form->handleRequest($request);

        if ($request->isMethod('POST')) {

            foreach ($etudiants as $etudiant) {
                foreach ($criteres as $critere) {
                    if ($request->request->has('etu_' . $etudiant->getId() . '_' . $critere->getId())) {
                        $noteCritere = new FicheNoteCritere();
                        $noteCritere->setEtudiant($etudiant);
                        $noteCritere->setCritere($critere);

                        // Récupération de la note associée dans le formulaire
                        // if (isset($formData['note'][$etudiant->getId()][$critere->getId()])) {
                        $noteValue = $request->request->get('etu_' . $etudiant->getId() . '_' . $critere->getId());
                        if ($noteValue === '') {
                            $noteValue = null;
                        } else {
                            $noteValue = floatval($noteValue);
                        }
                        $noteCritere->setNote($noteValue);
                        // }

                        // Persiste l'entité pour l'ajouter en base
                        $entityManager->persist($noteCritere);
                    }
                }
            }

            // Sauvegarde en base
            $entityManager->flush();
            $this->addFlash('success', 'Les notes ont été enregistrées avec succès.');

            return $this->redirectToRoute('app_fiche_evaluation', ['id' => $evaluation->getId()]);
        }


        return $this->render('note/index.html.twig', [
            'evaluation' => $evaluation,
            //'form_ajout_note' => $form->createView(),
            'etudiants' => $etudiants,
            'criteres' => $criteres,
        ]);
    }
}