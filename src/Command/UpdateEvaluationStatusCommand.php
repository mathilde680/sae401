<?php

namespace App\Command;

use App\Service\EvaluationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-evaluation-status',
    description: 'Met à jour le statut des évaluations.'
)]
class UpdateEvaluationStatusCommand extends Command
{
    private $evaluationService;

    public function __construct(EvaluationService $evaluationService)
    {
        parent::__construct();
        $this->evaluationService = $evaluationService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->evaluationService->updateEvaluationStatus();
        $output->writeln('Les statuts des évaluations ont été mis à jour avec succès.');
        return Command::SUCCESS;
    }
}