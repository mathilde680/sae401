<?php

namespace App\Controller;

use App\Entity\Alerte;
use App\Entity\Evaluation;
use App\Entity\FicheGrille;
use App\Entity\FicheGroupe;
use App\Entity\FicheNoteCritere;
use App\Entity\Groupe;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Event\GroupeEvent;
use App\Event\JeuEvent;
use App\EventListener\AjoutGroupe;
use App\Form\AjoutEvaluationGroupeType;
use App\Form\AjoutEvaluationType;
use App\Form\FicheMatiereType;
use App\Form\GrilleType;
use App\Repository\AlerteRepository;
use App\Repository\CritereRepository;
use App\Repository\EtudiantRepository;
use App\Repository\EvaluationRepository;
use App\Repository\FicheGrilleRepository;
use App\Repository\FicheGroupeRepository;
use App\Repository\GrilleRepository;
use App\Repository\GroupeRepository;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use App\Repository\ProfesseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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

            // Gestion du statut en fonction de la date
            $currentDate = new \DateTime(); // Date actuelle
            $evaluationDate = $evaluation->getDate(); // Date de l'évaluation

            if ($evaluationDate > $currentDate) {
                // Si éval est à venir
                $evaluation->setStatut('À venir');
            } elseif ($evaluationDate < $currentDate && $evaluation->getNotes()->isEmpty()) {
                // Si éval est déjà passée et il n'y a pas encore de notes
                $evaluation->setStatut('En correction');
            } elseif ($evaluationDate < $currentDate && !$evaluation->getNotes()->isEmpty()) {
                // Si éval est déjà passée et qu'il y a des notes
                $evaluation->setStatut('Noté');
            }

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
    public function ajout_groupe_evaluation(int $id, EventDispatcherInterface $eventDispatcher, Request $request, EntityManagerInterface $entityManager, MatiereRepository $matiereRepository, SessionInterface $session, EtudiantRepository $etudiantRepository, GroupeRepository $groupeRepository, FicheGroupeRepository $ficheGroupeRepository): Response
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

            $alerte = new Alerte();
            $alerte->setEvaluation($evaluation);
            $alerte->setFicheGroupe($ficheGroupe);
            $alerte->setMessage("a créé un nouveau groupe en ");
            $entityManager->persist($alerte);


            $entityManager->persist($evaluation);
            $entityManager->flush();
            $session->set('evaluation_id', $evaluation->getId());

            $event = new GroupeEvent($groupe);
            $eventDispatcher->dispatch($event, GroupeEvent::ADDED);


            return $this->redirectToRoute('app_evaluation_grille_ajout', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evaluation/ajoutGroupe.html.twig', [
            'form_evaluation' => $form->createView(),
            'matiere' => $matiere,
        ]);
    }

    #[Route('/evaluation/ajout/grille/{id}', name: 'app_evaluation_grille_ajout')]
    public function ajout_grille_evaluation(int $id, Request $request, CritereRepository $critereRepository, GrilleRepository $grilleRepository, EntityManagerInterface $entityManager, MatiereRepository $matiereRepository, EtudiantRepository $etudiantRepository, EvaluationRepository $evaluationRepository, ProfesseurRepository $professeurRepository, SessionInterface $session): Response
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
        //dd($etudiants);

        $ficheGrille = new FicheGrille();

        $form = $this->createForm(GrilleType::class, $ficheGrille, [
            'grilles' => $grilleProf,
        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la grille sélectionnée dans le formulaire
            $grille = $form->get('grille')->getData();
            $idGrille = $grille->getId();
            $criteres = $critereRepository->findBy([
                'Grille' => $idGrille,
            ]);
            // pour chaque étudiant on instancie une nouvelle fiche relier à l'eval et à la grille
            foreach ($etudiants as $etudiant) {
                $ficheEtudiant = new FicheGrille();
                $ficheEtudiant->setEtudiant($etudiant);
                $ficheEtudiant->setEvaluation($evaluation);

                if ($grille) {
                    $ficheEtudiant->setGrille($grille);
                }
                foreach ($criteres as $critere) {
                    $noteCritere = new FicheNoteCritere();
                    $noteCritere->setEtudiant($etudiant);
                    $noteCritere->setCritere($critere);
                    $entityManager->persist($noteCritere);
                }
                $entityManager->persist($ficheEtudiant);
            }


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
    public function supprime_evaluation(
        int                    $id,
        FicheGrilleRepository  $ficheGrilleRepository,
        EntityManagerInterface $entityManager,
        NoteRepository         $noteRepository,
        GroupeRepository       $groupeRepository,
        FicheGroupeRepository  $ficheGroupeRepository,
        AlerteRepository $alertesRepository,
    ): Response
    {
        // Récupérer l'entité Evaluation existante
        $evaluation = $entityManager->getRepository(Evaluation::class)->find($id);

        if (!$evaluation) {
            throw $this->createNotFoundException('Évaluation non trouvée');
        }

        $matiere = $evaluation->getMatiere();
        $matiereId = $matiere->getId();

        // 1. D'abord, supprimer toutes les fiches_groupe liées aux groupes de cette évaluation
        $groupes = $groupeRepository->findBy(['evaluation' => $evaluation]);
        foreach ($groupes as $groupe) {
            $fichesGroupe = $ficheGroupeRepository->findBy(['Groupe' => $groupe]);
            foreach ($fichesGroupe as $ficheGroupe) {
                $entityManager->remove($ficheGroupe);
            }
        }

        // 2. Ensuite, supprimer les notes associées à l'évaluation
        $notes = $noteRepository->findBy(['Evaluation' => $evaluation]);
        foreach ($notes as $note) {
            $entityManager->remove($note);
        }

        // 3. Supprimer les fiches grille associées à l'évaluation
        $fichesGrille = $ficheGrilleRepository->findBy(['Evaluation' => $evaluation]);
        foreach ($fichesGrille as $ficheGrille) {
            $entityManager->remove($ficheGrille);
        }

        // 4. Maintenant supprimer les groupes associés à l'évaluation
        foreach ($groupes as $groupe) {
            $entityManager->remove($groupe);
        }

        // 3. Supprimer les fiches grille associées à l'évaluation
        $alertes = $alertesRepository->findBy(['Evaluation' => $evaluation]);
        foreach ($alertes as $alerte) {
            $entityManager->remove($alerte);
        }

        // 5. Finalement supprimer l'évaluation
        $entityManager->remove($evaluation);

        // Exécuter toutes les suppressions
        $entityManager->flush();

        // Rediriger vers la liste des évaluations après la suppression
        return $this->redirectToRoute('app_fiche_matiere', ['id' => $matiereId]);
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

            // Gestion du statut en fonction de la date
            $currentDate = new \DateTime(); // Date actuelle
            $evaluationDate = $evaluation->getDate(); // Date de l'évaluation

            if ($evaluationDate > $currentDate) {
                // Si éval est à venir
                $evaluation->setStatut('À venir');
            } elseif ($evaluationDate < $currentDate && $evaluation->getNotes()->isEmpty()) {
                // Si éval est déjà passée et il n'y a pas encore de notes
                $evaluation->setStatut('En correction');
            } elseif ($evaluationDate < $currentDate && !$evaluation->getNotes()->isEmpty()) {
                // Si éval est déjà passée et qu'il y a des notes
                $evaluation->setStatut('Noté');
            }

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
