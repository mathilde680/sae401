<?php

namespace App\Entity;

use App\Repository\AlerteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlerteRepository::class)]
class Alerte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'alertes', cascade: ['persist', 'remove'])]
    private ?FicheGroupe $fiche_groupe = null;

    #[ORM\ManyToOne(inversedBy: 'alertes', cascade: ['persist', 'remove'])]
    private ?Evaluation $Evaluation = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFicheGroupe(): ?FicheGroupe
    {
        return $this->fiche_groupe;
    }

    public function setFicheGroupe(?FicheGroupe $fiche_groupe): static
    {
        $this->fiche_groupe = $fiche_groupe;

        return $this;
    }

    public function getEvaluation(): ?Evaluation
    {
        return $this->Evaluation;
    }

    public function setEvaluation(?Evaluation $Evaluation): static
    {
        $this->Evaluation = $Evaluation;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
