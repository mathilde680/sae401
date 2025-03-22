<?php

namespace App\Entity;

use App\Repository\FicheGroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Alerte>
     */
    #[ORM\OneToMany(targetEntity: Alerte::class, mappedBy: 'fiche_groupe')]
    private Collection $alertes;

    public function __construct()
    {
        $this->alertes = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Alerte>
     */
    public function getAlertes(): Collection
    {
        return $this->alertes;
    }

    public function addAlerte(Alerte $alerte): static
    {
        if (!$this->alertes->contains($alerte)) {
            $this->alertes->add($alerte);
            $alerte->setFicheGroupe($this);
        }

        return $this;
    }

    public function removeAlerte(Alerte $alerte): static
    {
        if ($this->alertes->removeElement($alerte)) {
            // set the owning side to null (unless already changed)
            if ($alerte->getFicheGroupe() === $this) {
                $alerte->setFicheGroupe(null);
            }
        }

        return $this;
    }
}
