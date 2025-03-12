<?php

namespace App\Entity;

use App\Repository\FicheMatiereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheMatiereRepository::class)]
class FicheMatiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ficheMatieres')]
    private ?Matiere $Matiere = null;

    #[ORM\ManyToOne(inversedBy: 'ficheMatieres')]
    private ?Professeur $Professeur = null;

    #[ORM\ManyToOne(inversedBy: 'Fiche_matiere')]
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne(inversedBy: 'Fiche_matiere')]
    private ?Professeur $professeur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->Matiere;
    }

    public function setMatiere(?Matiere $Matiere): static
    {
        $this->Matiere = $Matiere;

        return $this;
    }

    public function getProfesseur(): ?Professeur
    {
        return $this->Professeur;
    }

    public function setProfesseur(?Professeur $Professeur): static
    {
        $this->Professeur = $Professeur;

        return $this;
    }
}
