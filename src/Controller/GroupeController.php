<?php

namespace App\Controller;

use App\Repository\EvaluationRepository;
use App\Repository\GroupeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GroupeController extends AbstractController
{
    #[Route('/groupe/{id}', name: 'app_groupe')]
    public function index(int $id,GroupeRepository $groupeRepository, EvaluationRepository $evaluationRepository): Response
    {
        $user = $this->getUser();
        $idUser = $this->getUser()->getId();

        $evaluation = $evaluationRepository->find($id);
        $groupes = $groupeRepository->findGroupeByEvaluation($id, $user);
        return $this->render('groupe/index.html.twig', [
            'controller_name' => 'GroupeController',
            'groupes' => $groupes,
            'evaluation' => $evaluation,
        ]);
    }
}
