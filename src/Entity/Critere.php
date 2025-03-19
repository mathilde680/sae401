<?php

namespace App\Entity;

use App\Repository\CritereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CritereRepository::class)]
class Critere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

   #[ORM\ManyToOne(inversedBy: 'criteres')]
    private ?Grille $Grille = null;

    //#[ORM\ManyToOne(inversedBy: 'criteres', cascade: ["remove"])]
    //private ?Grille $Grille = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getGrille(): ?Grille
    {
        return $this->Grille;
    }

    public function setGrille(?Grille $Grille): static
    {
        $this->grille = $Grille;
        return $this;
    }

}
