<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Matiere;
use App\Form\AjoutEvaluationGroupeType;
use App\Form\AjoutEvaluationType;
use App\Form\MatiereType;
use App\Repository\MatiereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class EvaluationController extends AbstractController
{
    //pour ajouter une évaluation
    #[Route('/evaluation/ajout/{id}', name: 'app_evaluation_ajout')]
    public function ajout_evaluation(int $id, Request $request, EntityManagerInterface $entityManager, MatiereRepository $matiereRepository, SessionInterface $session): Response {

        $user = $this->getUser();

        // Récupérer la matière associée
        $matiere = $matiereRepository->find($id);

        // Création d'une nouvelle évaluation
        $evaluation = new Evaluation();
        $evaluation->setMatiere($matiere); // Associe la matière
        $evaluation->setProfesseur($user);

        // Création du formulaire avec l'évaluation pré-remplie
        $form = $this->createForm(AjoutEvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Stocker les données dans la session
            $session->set('evaluation_data', $form->getData());

            return $this->redirectToRoute('app_evaluation_groupe_ajout', ['id' => $id]);
        }

        return $this->render('evaluation/ajout.html.twig', [
            'form_evaluation' => $form->createView(),
            'matiere' => $matiere,
        ]);
    }

    #[Route('/evaluation/ajout/groupe/{id}', name: 'app_evaluation_groupe_ajout')]
    public function ajout_groupe_evaluation(int $id, Request $request, EntityManagerInterface $entityManager, MatiereRepository $matiereRepository, SessionInterface $session): Response {

        $user = $this->getUser();

        // Récupérer les données de réservation stockées dans la session
        $data = $session->get('evaluation_data', []);

        // Récupérer la matière associée
        $matiere = $matiereRepository->find($id);

        // Création d'une nouvelle évaluation
        $evaluation = new Evaluation();
        $evaluation->setMatiere($matiere); // Associe la matière
        $evaluation->setProfesseur($user);



        // Création du formulaire avec l'évaluation pré-remplie
        $form = $this->createForm(AjoutEvaluationGroupeType::class, $evaluation);
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
