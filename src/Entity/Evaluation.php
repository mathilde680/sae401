<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $coef = null;

    #[ORM\Column(length: 20)]
    private ?string $statut = null;

    #[ORM\Column(length: 20)]
    private ?string $statut_groupe = null;

    #[ORM\Column(nullable: true)]
    private ?int $taille_max_groupe = null;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'Evaluation')]
    private Collection $notes;

//    #[ORM\ManyToOne(inversedBy: 'evaluations')]
//    private ?Matiere $Matiere = null;

    /**
     * @var Collection<int, FicheGrille>
     */
    #[ORM\OneToMany(targetEntity: FicheGrille::class, mappedBy: 'Evaluation')]
    private Collection $ficheGrilles;

//    #[ORM\ManyToOne(inversedBy: 'evaluations')]
//    private ?Professeur $Professeur = null;

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\OneToMany(targetEntity: Groupe::class, mappedBy: 'Evaluation')]
    private Collection $groupes;

    /**
     * @var Collection<int, FicheGrille>
     */
    #[ORM\OneToMany(targetEntity: FicheGrille::class, mappedBy: 'evaluation')]
    private Collection $Fiche_grille;

    #[ORM\ManyToOne(inversedBy: 'Evaluation')]
    private ?Professeur $professeur = null;

    #[ORM\ManyToOne(inversedBy: 'Evaluation')]
    private ?Matiere $matiere = null;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'evaluation')]
    private Collection $Note;

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\OneToMany(targetEntity: Groupe::class, mappedBy: 'evaluation')]
    private Collection $Groupe;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $formation_groupe = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $type_groupe = null;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->ficheGrilles = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->Fiche_grille = new ArrayCollection();
        $this->Note = new ArrayCollection();
        $this->Groupe = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getCoef(): ?int
    {
        return $this->coef;
    }

    public function setCoef(int $coef): static
    {
        $this->coef = $coef;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getStatutGroupe(): ?string
    {
        return $this->statut_groupe;
    }

    public function setStatutGroupe(string $statut_groupe): static
    {
        $this->statut_groupe = $statut_groupe;

        return $this;
    }

    public function getTailleMaxGroupe(): ?int
    {
        return $this->taille_max_groupe;
    }

    public function setTailleMaxGroupe(?int $taille_max_groupe): static
    {
        $this->taille_max_groupe = $taille_max_groupe;

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setEvaluation($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getEvaluation() === $this) {
                $note->setEvaluation(null);
            }
        }

        return $this;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;
        $this->matiere = $matiere;
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
            $ficheGrille->setEvaluation($this);
        }

        return $this;
    }

    public function removeFicheGrille(FicheGrille $ficheGrille): static
    {
        if ($this->ficheGrilles->removeElement($ficheGrille)) {
            // set the owning side to null (unless already changed)
            if ($ficheGrille->getEvaluation() === $this) {
                $ficheGrille->setEvaluation(null);
            }
        }

        return $this;
    }

    public function getProfesseur(): ?Professeur
    {
        return $this->professeur;
    }

    public function setProfesseur(?Professeur $professeur): static
    {
        $this->professeur = $professeur;

        return $this;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): static
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes->add($groupe);
            $groupe->setEvaluation($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getEvaluation() === $this) {
                $groupe->setEvaluation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheGrille>
     */
    public function getFicheGrille(): Collection
    {
        return $this->Fiche_grille;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNote(): Collection
    {
        return $this->Note;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getGroupe(): Collection
    {
        return $this->Groupe;
    }

    public function getFormationGroupe(): ?string
    {
        return $this->formation_groupe;
    }

    public function setFormationGroupe(?string $formation_groupe): static
    {
        $this->formation_groupe = $formation_groupe;

        return $this;
    }

    public function getTypeGroupe(): ?string
    {
        return $this->type_groupe;
    }

    public function setTypeGroupe(?string $type_groupe): static
    {
        $this->type_groupe = $type_groupe;

        return $this;
    }

}
