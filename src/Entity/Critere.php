<?php

namespace App\Entity;

use App\Repository\CritereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

   /**
    * @var Collection<int, FicheNoteCritere>
    */
   #[ORM\OneToMany(targetEntity: FicheNoteCritere::class, mappedBy: 'Critere')]
   private Collection $ficheNoteCriteres;

   public function __construct()
   {
       $this->ficheNoteCriteres = new ArrayCollection();
   }

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
        $this->Grille = $Grille;
        return $this;
    }

    /**
     * @return Collection<int, FicheNoteCritere>
     */
    public function getFicheNoteCriteres(): Collection
    {
        return $this->ficheNoteCriteres;
    }

    public function addFicheNoteCritere(FicheNoteCritere $ficheNoteCritere): static
    {
        if (!$this->ficheNoteCriteres->contains($ficheNoteCritere)) {
            $this->ficheNoteCriteres->add($ficheNoteCritere);
            $ficheNoteCritere->setCritere($this);
        }

        return $this;
    }

    public function removeFicheNoteCritere(FicheNoteCritere $ficheNoteCritere): static
    {
        if ($this->ficheNoteCriteres->removeElement($ficheNoteCritere)) {
            // set the owning side to null (unless already changed)
            if ($ficheNoteCritere->getCritere() === $this) {
                $ficheNoteCritere->setCritere(null);
            }
        }

        return $this;
    }

}
