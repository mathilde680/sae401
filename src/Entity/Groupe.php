<?php
namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    // Correction: utiliser un nom de propriété en minuscule et cohérent
    #[ORM\ManyToOne(targetEntity: Evaluation::class, inversedBy: 'groupes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Evaluation $evaluation = null;

    /**
     * @var Collection<int, FicheGroupe>
     */
    #[ORM\OneToMany(targetEntity: FicheGroupe::class, mappedBy: 'groupe')]
    private Collection $ficheGroupes;

    #[ORM\Column]
    private ?int $taille = null;

    public function __construct()
    {
        $this->ficheGroupes = new ArrayCollection();
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

    // Correction: méthode mise à jour pour utiliser la propriété en minuscule
    public function getEvaluation(): ?Evaluation
    {
        return $this->evaluation;
    }

    // Correction: méthode mise à jour pour utiliser la propriété en minuscule
    public function setEvaluation(?Evaluation $evaluation): static
    {
        $this->evaluation = $evaluation;
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
            // Pas besoin de cette ligne, elle est redondante
            // $this->ficheGroupes->removeElement($ficheGroupe);
        }
        return $this;
    }

    public function getTaille(): ?int
    {
        return $this->taille;
    }

    public function setTaille(int $taille): static
    {
        $this->taille = $taille;
        return $this;
    }
}