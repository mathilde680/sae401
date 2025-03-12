<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatiereRepository::class)]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Evaluation>
     */
    #[ORM\OneToMany(targetEntity: Evaluation::class, mappedBy: 'Matiere')]
    private Collection $evaluations;

    /**
     * @var Collection<int, FicheMatiere>
     */
    #[ORM\OneToMany(targetEntity: FicheMatiere::class, mappedBy: 'Matiere')]
    private Collection $ficheMatieres;

    /**
     * @var Collection<int, FicheCours>
     */
    #[ORM\OneToMany(targetEntity: FicheCours::class, mappedBy: 'Matiere')]
    private Collection $ficheCours;

    /**
     * @var Collection<int, FicheCours>
     */
    #[ORM\OneToMany(targetEntity: FicheCours::class, mappedBy: 'matiere')]
    private Collection $Fiche_cours;

    /**
     * @var Collection<int, FicheMatiere>
     */
    #[ORM\OneToMany(targetEntity: FicheMatiere::class, mappedBy: 'matiere')]
    private Collection $Fiche_matiere;

    /**
     * @var Collection<int, Evaluation>
     */
    #[ORM\OneToMany(targetEntity: Evaluation::class, mappedBy: 'matiere')]
    private Collection $Evaluation;

    public function __construct()
    {
        $this->evaluations = new ArrayCollection();
        $this->ficheMatieres = new ArrayCollection();
        $this->ficheCours = new ArrayCollection();
        $this->Fiche_cours = new ArrayCollection();
        $this->Fiche_matiere = new ArrayCollection();
        $this->Evaluation = new ArrayCollection();
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

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): static
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations->add($evaluation);
            $evaluation->setMatiere($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): static
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getMatiere() === $this) {
                $evaluation->setMatiere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheMatiere>
     */
    public function getFicheMatieres(): Collection
    {
        return $this->ficheMatieres;
    }

    public function addFicheMatiere(FicheMatiere $ficheMatiere): static
    {
        if (!$this->ficheMatieres->contains($ficheMatiere)) {
            $this->ficheMatieres->add($ficheMatiere);
            $ficheMatiere->setMatiere($this);
        }

        return $this;
    }

    public function removeFicheMatiere(FicheMatiere $ficheMatiere): static
    {
        if ($this->ficheMatieres->removeElement($ficheMatiere)) {
            // set the owning side to null (unless already changed)
            if ($ficheMatiere->getMatiere() === $this) {
                $ficheMatiere->setMatiere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheCours>
     */
    public function getFicheCours(): Collection
    {
        return $this->ficheCours;
    }

    public function addFicheCour(FicheCours $ficheCour): static
    {
        if (!$this->ficheCours->contains($ficheCour)) {
            $this->ficheCours->add($ficheCour);
            $ficheCour->setMatiere($this);
        }

        return $this;
    }

    public function removeFicheCour(FicheCours $ficheCour): static
    {
        if ($this->ficheCours->removeElement($ficheCour)) {
            // set the owning side to null (unless already changed)
            if ($ficheCour->getMatiere() === $this) {
                $ficheCour->setMatiere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheMatiere>
     */
    public function getFicheMatiere(): Collection
    {
        return $this->Fiche_matiere;
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEvaluation(): Collection
    {
        return $this->Evaluation;
    }
}
