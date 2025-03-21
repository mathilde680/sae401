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
use App\Repository\GrilleRepository;
use App\Repository\GroupeRepository;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Range;

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
    public function evaluation_fiche(int $id, EvaluationRepository $evaluationRepository, FicheGrilleRepository $ficheGrilleRepository, CritereRepository $critereRepository): Response
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
        int                    $id, EvaluationRepository $evaluationRepository, Request $request,
        NoteRepository         $noteRepository,
        EntityManagerInterface $entityManager,
        FicheGrilleRepository  $ficheGrilleRepository,
        CritereRepository      $critereRepository,
        EtudiantRepository     $etudiantRepository,
    ): Response
    {
        // Je recupere l'id de l'eval
        $evaluation = $evaluationRepository->find($id);
        $idEvaluation = $evaluation->getId();

        if (!$evaluation) {
            throw $this->createNotFoundException('Évaluation non trouvée');
        }

        // je recupere tout les etudiants liée à la matiere et donc à l'eval
        $etudiants = $etudiantRepository->findEtudiantsByMatiereId($id);

        // Récupérer les notes associées à cette évaluation
        $notes = $evaluation->getNotes()->toArray(); // Convertir la collection en tableau

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
        $noteCritere = new FicheNoteCritere();

        // Trier les notes par nom en ordre alphabétique
        usort($notes, function ($a, $b) {
            // Supposant que vous avez une méthode getEleve()->getNom() pour accéder au nom
            return strcmp($a->getEtudiant()->getNom(), $b->getEtudiant()->getNom());
        });
        $notesData = [];

        foreach ($notes as $note) {
            $etudiantId = $note->getEtudiant()->getId();
            $notesData[$etudiantId] = [
                'etudiant' => $note->getEtudiant(),
                'criteres' => []
            ];

            foreach ($criteres as $critere) {
                $critereId = $critere->getId();
                // Rechercher si une note existe déjà pour cet étudiant et ce critère
                $existingNote = $entityManager->getRepository(FicheNoteCritere::class)->findOneBy([
                    'Etudiant' => $etudiantId,
                    'Critere' => $critereId
                ]);

                $notesData[$etudiantId]['criteres'][$critereId] = [
                    'critere' => $critere,
                    'valeur' => $existingNote ? $existingNote->getNote() : null
                ];
            }
        }

        $form = $this->createFormBuilder(['notes' => $notesData])
            ->add('notes', CollectionType::class, [
                'entry_type' => AjoutNoteType::class,
                'entry_options' => [
                    'criteres' => $criteres
                ],
                'allow_add' => false,
                'allow_delete' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer les notes',
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            foreach ($formData['notes'] as $etudiantId => $etudiantData) {
                $etudiant = $etudiantRepository->find($etudiantId);

                foreach ($etudiantData['criteres'] as $critereId => $critereData) {
                    $critere = $critereRepository->find($critereId);
                    $valeur = $critereData['valeur'];

                    if ($valeur !== null) {
                        // Vérifier si une note existe déjà
                        $noteCritere = $entityManager->getRepository(FicheNoteCritere::class)->findOneBy([
                            'etudiant' => $etudiantId,
                            'critere' => $critereId
                        ]);

                        if (!$noteCritere) {
                            $noteCritere = new FicheNoteCritere();
                            $noteCritere->setEtudiant($etudiant);
                            $noteCritere->setCritere($critere);
                        }

                        $noteCritere->setNote($valeur);
                        $entityManager->persist($noteCritere);
                    }
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Les notes ont été enregistrées avec succès.');

            return $this->redirectToRoute('app_fiche_evaluation', ['id' => $evaluation->getId()]);
        }


        return $this->render('note/index.html.twig', [
            'evaluation' => $evaluation,
            'notes' => $notes,
            'form_ajout_note' => $form,
            'etudiants' => $etudiants,
            'criteres'=>$criteres
        ]);
    }
}