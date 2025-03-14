<?php

namespace App\Entity;

use App\Repository\ProfesseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ProfesseurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Professeur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $prenom = null;

    /**
     * @var Collection<int, Grille>
     */
    #[ORM\OneToMany(targetEntity: Grille::class, mappedBy: 'Professeur')]
    private Collection $grilles;

    /**
     * @var Collection<int, FicheMatiere>
     */
    #[ORM\OneToMany(targetEntity: FicheMatiere::class, mappedBy: 'Professeur')]
    private Collection $ficheMatieres;

    /**
     * @var Collection<int, Evaluation>
     */
    #[ORM\OneToMany(targetEntity: Evaluation::class, mappedBy: 'Professeur')]
    private Collection $evaluations;

    /**
     * @var Collection<int, FicheMatiere>
     */
    #[ORM\OneToMany(targetEntity: FicheMatiere::class, mappedBy: 'professeur')]
    private Collection $Fiche_matiere;

    /**
     * @var Collection<int, Grille>
     */
    #[ORM\OneToMany(targetEntity: Grille::class, mappedBy: 'professeur')]
    private Collection $Grille;

    /**
     * @var Collection<int, Evaluation>
     */
    #[ORM\OneToMany(targetEntity: Evaluation::class, mappedBy: 'professeur')]
    private Collection $Evaluation;

    public function __construct()
    {
        $this->grilles = new ArrayCollection();
        $this->ficheMatieres = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->Fiche_matiere = new ArrayCollection();
        $this->Grille = new ArrayCollection();
        $this->Evaluation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_PROFESSEUR';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return Collection<int, Grille>
     */
    public function getGrilles(): Collection
    {
        return $this->grilles;
    }

    public function addGrille(Grille $grille): static
    {
        if (!$this->grilles->contains($grille)) {
            $this->grilles->add($grille);
            $grille->setProfesseur($this);
        }

        return $this;
    }

    public function removeGrille(Grille $grille): static
    {
        if ($this->grilles->removeElement($grille)) {
            // set the owning side to null (unless already changed)
            if ($grille->getProfesseur() === $this) {
                $grille->setProfesseur(null);
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
            $ficheMatiere->setProfesseur($this);
        }

        return $this;
    }

    public function removeFicheMatiere(FicheMatiere $ficheMatiere): static
    {
        if ($this->ficheMatieres->removeElement($ficheMatiere)) {
            // set the owning side to null (unless already changed)
            if ($ficheMatiere->getProfesseur() === $this) {
                $ficheMatiere->setProfesseur(null);
            }
        }

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
            $evaluation->setProfesseur($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): static
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getProfesseur() === $this) {
                $evaluation->setProfesseur(null);
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
     * @return Collection<int, Grille>
     */
    public function getGrille(): Collection
    {
        return $this->Grille;
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEvaluation(): Collection
    {
        return $this->Evaluation;
    }
}
