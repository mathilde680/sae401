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

    #[ORM\ManyToOne(inversedBy: 'ficheMatiere')]
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne(inversedBy: 'ficheMatiere')]
    private ?Professeur $Professeur = null;

//    #[ORM\ManyToOne(inversedBy: 'Fiche_matiere')]
//    private ?Matiere $matiere = null;
//
//    #[ORM\ManyToOne(inversedBy: 'Fiche_matiere')]
//    private ?Professeur $professeur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;

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
