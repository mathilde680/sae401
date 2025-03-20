<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\FicheGrille;
use App\Entity\FicheGroupe;
use App\Entity\Groupe;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Form\AjoutEvaluationGroupeType;
use App\Form\AjoutEvaluationType;
use App\Form\FicheMatiereType;
use App\Form\GrilleType;
use App\Repository\EtudiantRepository;
use App\Repository\EvaluationRepository;
use App\Repository\FicheGrilleRepository;
use App\Repository\GrilleRepository;
use App\Repository\GroupeRepository;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use App\Repository\ProfesseurRepository;
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
                // Stocker les données dans la session toute l'eval
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

                // Stock en session l'id de l'eval
                $session->set('evaluation_id', $evaluation->getId());

                return $this->redirectToRoute('app_evaluation_grille_ajout', ['id' => $id]);
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

                if ($formationGroupe === 'Aléatoire') {
                    shuffle($etudiantsGroupe);
                }

                // Calcul du nombre de groupes
                $nbGroupes = intdiv($nbEtudiants, $tailleGroupe); // Division entière
                $reste = $nbEtudiants % $tailleGroupe; // Reste des étudiants non répartis par modulo

                $etudiantIndex = 0;

                // Création des groupes complets
                for ($i = 1; $i <= $nbGroupes; $i++) {
                    $groupe = new Groupe();
                    $groupe->setEvaluation($evaluation);
                    $groupe->setNom("Groupe " . $typeGroupe . " " . $groupeKey . "-" . $i);
                    $groupe->setTaille($tailleGroupe);
                    $entityManager->persist($groupe);

                    // Assigner $tailleGroupe étudiants à ce groupe
                    for ($j = 0; $j < $tailleGroupe; $j++) {
                        if ($etudiantIndex < count($etudiantsGroupe)) {
                            $etudiant = $etudiantsGroupe[$etudiantIndex];

                            $ficheGroupe = new FicheGroupe();
                            $ficheGroupe->setEtudiant($etudiant);
                            $ficheGroupe->setGroupe($groupe);
                            $entityManager->persist($ficheGroupe);

                            $etudiantIndex++;
                        }
                    }

                }

                // Les etudiants restant
                if ($reste > 0) {
                    $groupe = new Groupe();
                    $groupe->setEvaluation($evaluation);
                    $groupe->setNom("Groupe " . $typeGroupe . " " . $groupeKey . "-" . ($nbGroupes + 1));
                    $groupe->setTaille($reste);
                    $entityManager->persist($groupe);

                    // Assigner les étudiants restants à ce dernier groupe
                    for ($j = 0; $j < $reste; $j++) {
                        if ($etudiantIndex < count($etudiantsGroupe)) {
                            $etudiant = $etudiantsGroupe[$etudiantIndex];

                            $ficheGroupe = new FicheGroupe();
                            $ficheGroupe->setEtudiant($etudiant);
                            $ficheGroupe->setGroupe($groupe);
                            $entityManager->persist($ficheGroupe);

                            $etudiantIndex++;
                        }
                    }
                }
            }

            $entityManager->persist($evaluation);
            //$entityManager->flush();

            return $this->redirectToRoute('app_evaluation_grille_ajout', ['id' => $id]);
        }

        return $this->render('evaluation/ajoutGroupe.html.twig', [
            'form_evaluation' => $form->createView(),
            'matiere' => $matiere,
        ]);
    }

    #[Route('/evaluation/ajout/grille/{id}', name: 'app_evaluation_grille_ajout')]
    public function ajout_grille_evaluation(int $id, Request $request, GrilleRepository $grilleRepository, EntityManagerInterface $entityManager, MatiereRepository $matiereRepository, EtudiantRepository $etudiantRepository, EvaluationRepository $evaluationRepository, ProfesseurRepository $professeurRepository,  SessionInterface $session): Response
    {
        // Recupere id prof
        $user = $this->getUser();
        $profId = $user->getId();

        // Recupere la matiére
        $matiere = $matiereRepository->find($id);

        // Recupere les etudiants
        $etudiants = $etudiantRepository->findEtudiantsByMatiereId($id);

        // Recupere les grilles du profs
        $grilleProf = $grilleRepository->findAllByProfesseur($profId);

        // Recupere id de l'eval en session
        $evaluationId = $session->get('evaluation_id');
        if (!$evaluationId) {
            throw $this->createNotFoundException('Aucune évaluation trouvée en session.');
        }

        // Recupere l'evaluation
        $evaluation = $evaluationRepository->find($evaluationId);
        if (!$evaluation) {
            throw $this->createNotFoundException('Évaluation introuvable.');
        }

        // pour chaque étudiant on instancie une nouvelle fiche relier à l'eval et à la grille
        foreach ($etudiants as $etudiant) {
            $ficheGrille = new FicheGrille();
            $ficheGrille->setEtudiant($etudiant);
            $ficheGrille->setEvaluation($evaluation);

            $entityManager->persist($ficheGrille);
        }

        $form = $this->createForm(GrilleType::class, $ficheGrille, [
            'grilles' =>$grilleProf,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();
            // Vide la session
            $session->remove('evaluation_data');
            return $this->redirectToRoute('app_fiche_matiere', ['id' => $id]);
        }

        return $this->render('evaluation/formEvaluationGrille.html.twig', [
            'form_evaluation' => $form->createView(),
            'grilles' => $grilleProf,
        ]);
    }

    //pour supprimer une évaluation
    #[Route('/evaluation/{id}/supprime', name: 'app_evaluation_supprime', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprime_evaluation(int $id, FicheGrilleRepository $ficheGrilleRepository, EntityManagerInterface $entityManager, NoteRepository $noteRepository, GroupeRepository $groupeRepository): Response
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

            $ficheGrilles = $ficheGrilleRepository->findBy(['Evaluation' => $evaluation]);
            if($ficheGrilles){
                foreach ($ficheGrilles as $ficheGrille) {
                    $entityManager->remove($ficheGrille);
                }
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
        return $this->render('evaluation/modif.html.twig', [
            'form_evaluation' => $form->createView(),
            'id' => $id,
            'evaluation' => $form->getData(),
        ]);
    }
}
