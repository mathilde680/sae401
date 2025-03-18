<?php

namespace App\Entity;

use App\Repository\FicheCoursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheCoursRepository::class)]
class FicheCours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ficheCours')]
    private ?Etudiant $Etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'ficheCours')]
    private ?Matiere $Matiere = null;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getMatiere(): ?Matiere
    {
        return $this->Matiere;
    }

    public function setMatiere(?Matiere $Matiere): static
    {
        $this->Matiere = $Matiere;

        return $this;
    }
}
