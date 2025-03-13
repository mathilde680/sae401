<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Matiere;
use App\Form\AjoutEvaluationType;
use App\Form\MatiereType;
use App\Repository\EvaluationRepository;
use App\Repository\MatiereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MatiereController extends AbstractController
{

    #[Route('/fiche/{id}', name: 'app_fiche_matiere', requirements: ['id'=>'\d+'])]
    public function matiere_fiche(int $id, MatiereRepository $matiereRepository, EvaluationRepository $evaluationRepository): Response
    {
        $fiches = $matiereRepository->find($id);
        $evaluations = $evaluationRepository->findBy(['matiere' => $id]);

        return $this->render('matiere/fiche.html.twig', [
            'fiches' => $fiches,
            'evaluations' => $evaluations,
        ]);
    }

    #[Route('/matiere/ajout', name: 'app_matiere_ajout')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de matiere
        $matiere = new Matiere();

        // Créer le formulaire
        $form = $this->createForm(MatiereType::class,$matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persister et enregistrer le matiere dans la base de données
            $entityManager->persist($matiere);
            $entityManager->flush();

            // Rediriger vers une autre page (par exemple, la liste des matieres)
            return $this->redirectToRoute('app_accueil_prof');

        }

        // Afficher le formulaire
        return $this->render('matiere/ajout.html.twig', [
            'form_matiere' => $form->createView(),
            'matiere' => $form->getData(),
        ]);
    }

    #[Route('/matiere/{id}/supprime', name: 'app_matiere_supprime', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprime(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'entité Jeu existante
        $matiere = $entityManager->getRepository(Matiere::class)->find($id);


        // Supprimer le jeu de la base de données
        $entityManager->remove($matiere);
        $entityManager->flush();

        // Rediriger vers la liste des jeux après la suppression
        return $this->redirectToRoute('app_accueil_prof');
    }

    #[Route('/matiere/{id}/modif', name: 'app_matiere_modif', requirements: ['id' => '\d+'])]
    public function modif(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $matiere = $entityManager->getRepository(Matiere::class)->find($id);
        if (!$matiere) {
            throw $this->createNotFoundException('Matière Inexistant');
        }

        $form = $this->createForm(MatiereType::class, $matiere);
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

    //pour ajouter une évaluation
    #[Route('/evaluation/ajout/{id}', name: 'app_evaluation_ajout')]
    public function ajout_evaluation(int $id, Request $request, EntityManagerInterface $entityManager, MatiereRepository $matiereRepository,): Response {

        // Récupérer la matière associée
        $matiere = $matiereRepository->find($id);

        // Création d'une nouvelle évaluation
        $evaluation = new Evaluation();
        $evaluation->setMatiere($matiere); // Associe la matière

        // Création du formulaire avec l'évaluation pré-remplie
        $form = $this->createForm(AjoutEvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($evaluation);
            $entityManager->flush();

            return $this->redirectToRoute('app_fiche_matiere', ['id' => $id]);
        }

        return $this->render('evaluation/ajout.html.twig', [
            'form_evaluation' => $form->createView(),
            'matiere' => $matiere,
        ]);
    }


    //pour supprimer une évaluation
    #[Route('/evaluation/{id}/supprime', name: 'app_evaluation_supprime', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprime_evaluation(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'entité Evaluation existante
        $evaluation = $entityManager->getRepository(Evaluation::class)->find($id);

        // Supprimer l'évaluation de la base de données
        $entityManager->remove($evaluation);
        $entityManager->flush();

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


}
