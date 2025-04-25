<?php

namespace App\Entity;

use App\Repository\FicheNoteCritereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheNoteCritereRepository::class)]
class FicheNoteCritere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $note = null;

    #[ORM\ManyToOne(inversedBy: 'ficheNoteCriteres')]
    private ?Etudiant $Etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'ficheNoteCriteres')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Critere $Critere = null;

    #[ORM\ManyToOne(inversedBy: 'ficheNoteCriteres')]
    private ?Evaluation $Evaluation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->Etudiant;
    }

    public function setEtudiant(?Etudiant $Etudiant): static
    {
        $this->Etudiant = $Etudiant;

        return $this;
    }

    public function getCritere(): ?Critere
    {
        return $this->Critere;
    }

    public function setCritere(?Critere $Critere): static
    {
        $this->Critere = $Critere;

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
}
