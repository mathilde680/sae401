<?php

namespace App\Entity;

use App\Repository\GrilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GrilleRepository::class)]
class Grille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'grilles')]
    private ?Professeur $Professeur = null;

    /**
     * @var Collection<int, Critere>
     */
    #[ORM\OneToMany(targetEntity: Critere::class, mappedBy: 'Grille')]
    private Collection $criteres;

    /**
     * @var Collection<int, FicheGrille>
     */
    #[ORM\OneToMany(targetEntity: FicheGrille::class, mappedBy: 'Grille')]
    private Collection $ficheGrilles;

    #[ORM\ManyToOne(inversedBy: 'Grille')]
    private ?Professeur $professeur = null;

    /**
     * @var Collection<int, Critere>
     */
//    #[ORM\OneToMany(targetEntity: Critere::class, mappedBy: 'grille')]
//    private Collection $Critere;

    /**
     * @var Collection<int, FicheGrille>
     */
    #[ORM\OneToMany(targetEntity: FicheGrille::class, mappedBy: 'grille')]
    private Collection $Fiche_grille;

    public function __construct()
    {
        $this->criteres = new ArrayCollection();
        $this->ficheGrilles = new ArrayCollection();
//        $this->Critere = new ArrayCollection();
        $this->Fiche_grille = new ArrayCollection();
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

    public function getProfesseur(): ?Professeur
    {
        return $this->Professeur;
    }

    public function setProfesseur(?Professeur $Professeur): static
    {
        $this->Professeur = $Professeur;

        return $this;
    }

    /**
     * @return Collection<int, Critere>
     */
    public function getCriteres(): Collection
    {
        return $this->criteres;
    }

    public function addCritere(Critere $critere): static
    {
        if (!$this->criteres->contains($critere)) {
            $this->criteres->add($critere);
            $critere->setGrille($this);
        }

        return $this;
    }

    public function removeCritere(Critere $critere): static
    {
        if ($this->criteres->removeElement($critere)) {
            // set the owning side to null (unless already changed)
            if ($critere->getGrille() === $this) {
                $critere->setGrille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheGrille>
     */
    public function getFicheGrilles(): Collection
    {
        return $this->ficheGrilles;
    }

    public function addFicheGrille(FicheGrille $ficheGrille): static
    {
        if (!$this->ficheGrilles->contains($ficheGrille)) {
            $this->ficheGrilles->add($ficheGrille);
            $ficheGrille->setGrille($this);
        }

        return $this;
    }

    public function removeFicheGrille(FicheGrille $ficheGrille): static
    {
        if ($this->ficheGrilles->removeElement($ficheGrille)) {
            // set the owning side to null (unless already changed)
            if ($ficheGrille->getGrille() === $this) {
                $ficheGrille->setGrille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Critere>
     */
    public function getCritere(): Collection
    {
        return $this->Critere;
    }

    /**
     * @return Collection<int, FicheGrille>
     */
    public function getFicheGrille(): Collection
    {
        return $this->Fiche_grille;
    }
}
