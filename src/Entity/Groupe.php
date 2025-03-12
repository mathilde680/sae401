<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'groupes')]
    private ?Evaluation $Evaluation = null;

    /**
     * @var Collection<int, FicheGroupe>
     */
    #[ORM\OneToMany(targetEntity: FicheGroupe::class, mappedBy: 'Groupe')]
    private Collection $ficheGroupes;

    #[ORM\ManyToOne(inversedBy: 'Groupe')]
    private ?Evaluation $evaluation = null;

    /**
     * @var Collection<int, FicheGroupe>
     */
    #[ORM\OneToMany(targetEntity: FicheGroupe::class, mappedBy: 'groupe')]
    private Collection $Fiche_groupe;

    public function __construct()
    {
        $this->ficheGroupes = new ArrayCollection();
        $this->Fiche_groupe = new ArrayCollection();
    }

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

    public function getEvaluation(): ?Evaluation
    {
        return $this->Evaluation;
    }

    public function setEvaluation(?Evaluation $Evaluation): static
    {
        $this->Evaluation = $Evaluation;

        return $this;
    }

    /**
     * @return Collection<int, FicheGroupe>
     */
    public function getFicheGroupes(): Collection
    {
        return $this->ficheGroupes;
    }

    public function addFicheGroupe(FicheGroupe $ficheGroupe): static
    {
        if (!$this->ficheGroupes->contains($ficheGroupe)) {
            $this->ficheGroupes->add($ficheGroupe);
            $ficheGroupe->setGroupe($this);
        }

        return $this;
    }

    public function removeFicheGroupe(FicheGroupe $ficheGroupe): static
    {
        if ($this->ficheGroupes->removeElement($ficheGroupe)) {
            // set the owning side to null (unless already changed)
            if ($ficheGroupe->getGroupe() === $this) {
                $ficheGroupe->setGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheGroupe>
     */
    public function getFicheGroupe(): Collection
    {
        return $this->Fiche_groupe;
    }
}
