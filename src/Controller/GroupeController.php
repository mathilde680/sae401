<?php

namespace App\Controller;

use App\Entity\FicheGroupe;
use App\Repository\EvaluationRepository;
use App\Repository\FicheGroupeRepository;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GroupeController extends AbstractController
{
    #[Route('/groupe/{id}', name: 'app_groupe')]
    public function index(int $id,GroupeRepository $groupeRepository, EvaluationRepository $evaluationRepository, FicheGroupeRepository $ficheGroupeRepository): Response
    {
        $user = $this->getUser();
        $idUser = $user->getId();

        $evaluation = $evaluationRepository->find($id);
        $groupes = $groupeRepository->findGroupeByEvaluation($id, $user);

        //si l'étudiant est déjà dans un groupe de cette évaluation
        $ficheGroupeExistante = $ficheGroupeRepository->findOneBy([
            'Etudiant' => $user,
            'Groupe' => $groupeRepository->findBy([
                'evaluation' => $id
            ])
        ]);

        // Si étudiant dans un groupe, info du groupe :
        $groupeActuel = $ficheGroupeExistante ? $ficheGroupeExistante->getGroupe() : null;

        return $this->render('groupe/index.html.twig', [
            'controller_name' => 'GroupeController',
            'groupes' => $groupes,
            'evaluation' => $evaluation,
            'groupeActuel' => $groupeActuel
        ]);
    }
    #[Route('/groupe/{id}/ajout', name: 'app_groupe_ajout', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function ajoutEtudiantGroupe(int $id, EntityManagerInterface $entityManager, GroupeRepository $groupeRepository, FicheGroupeRepository $ficheGroupeRepository): Response
    {
        $user = $this->getUser();
        $groupe = $groupeRepository->find($id);

        if (!$groupe) {
            throw $this->createNotFoundException('Ce groupe n\'existe pas.');
        }

        // si l'utilisateur est déjà dans un groupe de cette évaluation
        $evaluationId = $groupe->getEvaluation()->getId();
        $groupesDeLEvaluation = $groupeRepository->findBy(['evaluation' => $evaluationId]);

        foreach ($groupesDeLEvaluation as $g) {
            $existingFiche = $ficheGroupeRepository->findOneBy([
                'Etudiant' => $user,
                'Groupe' => $g
            ]);

            if ($existingFiche) {
                $this->addFlash('warning', 'Vous êtes déjà membre d\'un groupe pour cette évaluation. Veuillez d\'abord quitter ce groupe.');
                return $this->redirectToRoute('app_groupe', ['id' => $evaluationId]);
            }
        }

        // Ajoute utilisateur dans la table fiche_groupe
        $ficheGroupe = new FicheGroupe();
        $ficheGroupe->setEtudiant($user);
        $ficheGroupe->setGroupe($groupe);

        $entityManager->persist($ficheGroupe);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez rejoint le groupe avec succès.');
        return $this->redirectToRoute('app_groupe', ['id' => $groupe->getEvaluation()->getId()]);
    }

    #[Route('/groupe/{id}/quitter', name: 'app_groupe_quitter', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function quitterGroupe(int $id, EntityManagerInterface $entityManager, GroupeRepository $groupeRepository, FicheGroupeRepository $ficheGroupeRepository): Response
    {
        $user = $this->getUser();
        $groupe = $groupeRepository->find($id);

        if (!$groupe) {
            throw $this->createNotFoundException('Ce groupe n\'existe pas.');
        }

        // Recherche si l'utilisateur est dans ce groupe
        $ficheGroupe = $ficheGroupeRepository->findOneBy([
            'Etudiant' => $user,
            'Groupe' => $groupe
        ]);

        if (!$ficheGroupe) {
            $this->addFlash('warning', 'Vous n\'êtes pas membre de ce groupe.');
            return $this->redirectToRoute('app_groupe', ['id' => $groupe->getEvaluation()->getId()]);
        }

        // Supprime l'utilisateur du groupe
        $entityManager->remove($ficheGroupe);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez quitté le groupe avec succès.');
        return $this->redirectToRoute('app_groupe', ['id' => $groupe->getEvaluation()->getId()]);
    }
}
