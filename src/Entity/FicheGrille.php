<?php

namespace App\Entity;

use App\Repository\FicheGrilleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheGrilleRepository::class)]
class FicheGrille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ficheGrilles')]
    private ?Grille $Grille = null;

    #[ORM\ManyToOne(inversedBy: 'ficheGrilles', cascade: ['persist'])]
    private ?Evaluation $Evaluation = null;

    #[ORM\ManyToOne(inversedBy: 'ficheGrilles')]
    private ?Etudiant $Etudiant = null;

/*    #[ORM\ManyToOne(inversedBy: 'Fiche_grille')]
    private ?Grille $grille = null;

    #[ORM\ManyToOne(inversedBy: 'Fiche_grille')]
    private ?Evaluation $evaluation = null;

    #[ORM\ManyToOne(inversedBy: 'Fiche_grille')]
    private ?Etudiant $etudiant = null;*/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrille(): ?Grille
    {
        return $this->Grille;
    }

    public function setGrille(?Grille $Grille): static
    {
        $this->Grille = $Grille;

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

    public function getEtudiant(): ?Etudiant
    {
        return $this->Etudiant;
    }

    public function setEtudiant(?Etudiant $Etudiant): static
    {
        $this->Etudiant = $Etudiant;

        return $this;
    }
}
