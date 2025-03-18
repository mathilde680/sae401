<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\FicheMatiere;
use App\Entity\Matiere;
use App\Form\AjoutEvaluationType;
use App\Form\FicheMatiereType;
use App\Repository\EvaluationRepository;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MatiereController extends AbstractController
{

    #[Route('/fiche/{id}', name: 'app_fiche_matiere', requirements: ['id'=>'\d+'])]
    public function matiere_fiche(int $id, MatiereRepository $matiereRepository, EvaluationRepository $evaluationRepository, NoteRepository $noteRepository): Response
    {
        $fiches = $matiereRepository->find($id);
        $evaluations = $evaluationRepository->findBy(['matiere' => $id]);

        $notesMoy = $noteRepository->findNoteByMoyenne();
        $notesMin = $noteRepository->findNoteByMin();
        $notesMax = $noteRepository->findNoteByMax();


        return $this->render('matiere/fiche.html.twig', [
            'fiches' => $fiches,
            'evaluations' => $evaluations,
            'notesMoy' => $notesMoy,
            'notesMin' => $notesMin,
            'notesMax' => $notesMax,
        ]);
    }

    #[Route('/matiere/ajout', name: 'app_matiere_ajout')]
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

    #[Route('/matiere/{id}/supprime', name: 'app_matiere_supprime', requirements: ['id' => '\d+'], methods: ['POST'])]
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

    #[Route('/matiere/{id}/modif', name: 'app_matiere_modif', requirements: ['id' => '\d+'])]
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
