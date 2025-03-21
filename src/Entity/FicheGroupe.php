<?php

namespace App\Entity;

use App\Repository\FicheGroupeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheGroupeRepository::class)]
class FicheGroupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

//    #[ORM\ManyToOne(inversedBy: 'ficheGroupes')]
//    private ?Groupe $Groupe = null;

//    #[ORM\ManyToOne(inversedBy: 'ficheGroupes')]
//    private ?Etudiant $Etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'Fiche_groupe')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Groupe $Groupe = null;

    #[ORM\ManyToOne(inversedBy: 'Fiche_groupe')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Etudiant $Etudiant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->Groupe;
    }

    public function setGroupe(?Groupe $Groupe): static
    {
        $this->Groupe = $Groupe;

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
