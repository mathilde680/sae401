<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Groupe;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Form\AjoutEvaluationGroupeType;
use App\Form\AjoutEvaluationType;
use App\Form\FicheMatiereType;
use App\Repository\EtudiantRepository;
use App\Repository\EvaluationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class EvaluationController extends AbstractController
{
    // pour ajouter une évaluation
    #[Route('/evaluation/ajout/{id}', name: 'app_evaluation_ajout')]
    public function ajout_evaluation(int $id, Request $request, EntityManagerInterface $entityManager, MatiereRepository $matiereRepository, EtudiantRepository $etudiantRepository, NoteRepository $noteRepository, SessionInterface $session): Response
    {

        $user = $this->getUser();

        // Récupérer la matière associée
        $matiere = $matiereRepository->find($id);
        $etudiants = $etudiantRepository->findEtudiantsByMatiereId($id);

        // Création d'une nouvelle évaluation
        $evaluation = new Evaluation();
        $evaluation->setMatiere($matiere); // Associe la matière
        $evaluation->setProfesseur($user);

        // Création du formulaire avec l'évaluation pré-remplie
        $form = $this->createForm(AjoutEvaluationType::class, $evaluation);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('statut_groupe')->getData() === 'Groupe') {
                // Stocker les données dans la session
                $session->set('evaluation_data', $form->getData());
                return $this->redirectToRoute('app_evaluation_groupe_ajout', ['id' => $id]);
            } else {

                // Création des étudiants selon le semestre et la matière
                foreach ($etudiants as $etudiant) {
                    $note = new Note();
                    $note->setEtudiant($etudiant);
                    $note->setEvaluation($evaluation);
                    $entityManager->persist($note);
                    // dd($note);
                }

                // Envoie à la bdd
                $entityManager->persist($evaluation);
                $entityManager->flush();
                // Vide la session
                $session->remove('evaluation_data');
                return $this->redirectToRoute('app_fiche_matiere', ['id' => $id]);
            }
        }

        return $this->render('evaluation/ajout.html.twig', [
            'form_evaluation' => $form->createView(),
            'matiere' => $matiere,
        ]);
    }

    #[Route('/evaluation/ajout/groupe/{id}', name: 'app_evaluation_groupe_ajout')]
    public function ajout_groupe_evaluation(int $id, Request $request, EntityManagerInterface $entityManager, MatiereRepository $matiereRepository, SessionInterface $session, EtudiantRepository $etudiantRepository): Response
    {
        $user = $this->getUser();
        $matiere = $matiereRepository->find($id);

        // Récupérer les données d'évaluation stockées dans la session
        $evaluationData = $session->get('evaluation_data');

        // Création d'une nouvelle évaluation
        $evaluation = new Evaluation();
        $evaluation->setMatiere($matiere);
        $evaluation->setProfesseur($user);

        // RECUPERER LES INFOS DANS LA SESSION
        $evaluation->setNom($evaluationData->getNom());
        $evaluation->setDate($evaluationData->getDate());
        $evaluation->setCoef($evaluationData->getCoef());
        $evaluation->setStatutGroupe($evaluationData->getStatutGroupe());

        if ($evaluationData->getStatut() !== null) {
            $evaluation->setStatut($evaluationData->getStatut());
        } else {
            $evaluation->setStatut('Publiée');
        }

        // Création des notes pour tous les étudiants
        $etudiants = $etudiantRepository->findEtudiantsByMatiereId($id);
        foreach ($etudiants as $etudiant) {
            $note = new Note();
            $note->setEtudiant($etudiant);
            $note->setEvaluation($evaluation);
            $entityManager->persist($note);
        }

        // Création du formulaire avec l'évaluation pré-remplie
        $form = $this->createForm(AjoutEvaluationGroupeType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tailleGroupe = $form->get('taille_max_groupe')->getData();
            $typeGroupe = $form->get('type_groupe')->getData();
            $formationGroupe = $form->get('formation_groupe')->getData();

            // Récupérer les étudiants par type de groupe
            switch ($typeGroupe) {
                case 'TP':
                    $groupesEtudiants = $etudiantRepository->findEtudiantsByTp($id);
                    break;
                case 'TD':
                    $groupesEtudiants = $etudiantRepository->findEtudiantsByTd($id);
                    break;
                case 'Promotion':
                    $groupesEtudiants = $etudiantRepository->findEtudiantsByPromotion($id);
                    break;
                default:
                    $groupesEtudiants = [];
            }

            // Pour chaque groupe existant (TP, TD ou Promotion)
            foreach ($groupesEtudiants as $groupeKey => $etudiantsGroupe) {
                $nbEtudiants = count($etudiantsGroupe);

                // Calcul du nombre de groupes
                $nbGroupes = intdiv($nbEtudiants, $tailleGroupe); // Division entière
                $reste = $nbEtudiants % $tailleGroupe; // Reste des étudiants non répartis par modulo

                // Création des groupes complets
                for ($i = 1; $i <= $nbGroupes; $i++) {
                    $groupe = new Groupe();
                    $groupe->setEvaluation($evaluation);
                    $groupe->setNom("Groupe " . $typeGroupe . " " . $groupeKey . "-" . $i);
                    $groupe->setTaille($tailleGroupe);
                    $entityManager->persist($groupe);

                    // Formation aléatoire
                    if ($formationGroupe === 'Aléatoire') {
                    }
                }

                // Les etudiants restant
                if ($reste > 0) {
                    $groupe = new Groupe();
                    $groupe->setEvaluation($evaluation);
                    $groupe->setNom("Groupe " . $typeGroupe . " " . $groupeKey . "-" . ($nbGroupes + 1));
                    $groupe->setTaille($reste);
                    $entityManager->persist($groupe);
                }
            }

            $entityManager->persist($evaluation);
            $entityManager->flush();
            $session->remove('evaluation_data');
            return $this->redirectToRoute('app_fiche_matiere', ['id' => $id]);
        }

        return $this->render('evaluation/ajoutGroupe.html.twig', [
            'form_evaluation' => $form->createView(),
            'matiere' => $matiere,
        ]);
    }

    //pour supprimer une évaluation
    #[Route('/evaluation/{id}/supprime', name: 'app_evaluation_supprime', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprime_evaluation(int $id, EntityManagerInterface $entityManager, NoteRepository $noteRepository, GroupeRepository $groupeRepository): Response
    {
        // Récupérer l'entité Evaluation existante
        $evaluation = $entityManager->getRepository(Evaluation::class)->find($id);

        // Supprimer l'évaluation de la base de données
        if ($evaluation) {
            // Supprimer les notes associées à l'évaluation
            $notes = $noteRepository->findBy(['Evaluation' => $evaluation]);
            foreach ($notes as $note) {
                $entityManager->remove($note);
            }

            $groupes = $groupeRepository->findBy(['evaluation' => $evaluation]);
            foreach ($groupes as $groupe) {
                $entityManager->remove($groupe);
            }

            // Supprimer l'évaluation
            $entityManager->remove($evaluation);
            $entityManager->flush();
        }

        // Rediriger vers la liste des évaluations après la suppression
        return $this->redirectToRoute('app_accueil_prof');
    }

    // pour modifier une évaluation
    #[Route('/evaluation/{id}/modif', name: 'app_evaluation_modif', requirements: ['id' => '\d+'])]
    public function modif_evaluation(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $evaluation = $entityManager->getRepository(Evaluation::class)->find($id);
        if (!$evaluation) {
            throw $this->createNotFoundException('Evaluation Inexistant');
        }

        $form = $this->createForm(AjoutEvaluationType::class, $evaluation);
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Persister et enregistrer les modifications dans la base de données
            $entityManager->persist($evaluation);
            $entityManager->flush();

            // Rediriger vers la fiche du Jeu
            return $this->redirectToRoute('app_accueil_prof');
        }

        //affiche le formulaire
        return $this->render('evaluation/ajout.html.twig', [
            'form_evaluation' => $form->createView(),
            'id' => $id,
            'evaluation' => $form->getData(),
        ]);
    }

    #[Route('/evaluation/{id}', name: 'app_fiche_evaluation', requirements: ['id' => '\d+'])]
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

        return $this->render('matiere/evaluation.html.twig', [
            'evaluation' => $evaluation,
            'notes' => $notes,
        ]);
    }
}
