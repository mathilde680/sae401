<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Evaluation;

class EvaluationService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateEvaluationStatus(): void
    {
        // Récupère toutes les évaluations
        $evaluations = $this->entityManager->getRepository(Evaluation::class)->findAll();

        foreach ($evaluations as $evaluation) {
            // Vérifie la date de l'évaluation et met à jour le statut
            $currentDate = new \DateTime();
            $evaluationDate = $evaluation->getDate(); // Suppose qu'il y a un champ 'date' dans l'entité Evaluation

            if ($evaluationDate > $currentDate) {
                // Si la date de l'évaluation est dans le futur
                $evaluation->setStatut('A venir');
            } elseif ($evaluation->getNote() !== null) {
                // Si l'évaluation a une note (et la date est passée car on est dans le else)
                $evaluation->setStatut('Noté');
            } else {
                // Si la date est passée et qu'il n'y a pas de note
                $evaluation->setStatut('En correction');
            }

            // Persiste les modifications
            $this->entityManager->persist($evaluation);
        }

        // Sauvegarde les changements dans la base de données
        $this->entityManager->flush();
    }
}
