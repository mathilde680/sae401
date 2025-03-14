<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Etudiant implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $promotion = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $TD = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $TP = null;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'Etudiant')]
    private Collection $notes;

    /**
     * @var Collection<int, FicheGrille>
     */
    #[ORM\OneToMany(targetEntity: FicheGrille::class, mappedBy: 'Etudiant')]
    private Collection $ficheGrilles;

    /**
     * @var Collection<int, FicheGroupe>
     */
    #[ORM\OneToMany(targetEntity: FicheGroupe::class, mappedBy: 'Etudiant')]
    private Collection $ficheGroupes;

    /**
     * @var Collection<int, FicheCours>
     */
    #[ORM\OneToMany(targetEntity: FicheCours::class, mappedBy: 'Etudiant')]
    private Collection $ficheCours;

    /**
     * @var Collection<int, FicheCours>
     */
    #[ORM\OneToMany(targetEntity: FicheCours::class, mappedBy: 'etudiant')]
    private Collection $Fiche_cours;

    /**
     * @var Collection<int, FicheGrille>
     */
    #[ORM\OneToMany(targetEntity: FicheGrille::class, mappedBy: 'etudiant')]
    private Collection $Fiche_grille;

    /**
     * @var Collection<int, FicheGroupe>
     */
    #[ORM\OneToMany(targetEntity: FicheGroupe::class, mappedBy: 'etudiant')]
    private Collection $Fiche_groupe;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'etudiant')]
    private Collection $Note;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->ficheGrilles = new ArrayCollection();
        $this->ficheGroupes = new ArrayCollection();
        $this->ficheCours = new ArrayCollection();
        $this->Fiche_cours = new ArrayCollection();
        $this->Fiche_grille = new ArrayCollection();
        $this->Fiche_groupe = new ArrayCollection();
        $this->Note = new ArrayCollection();
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
        $roles[] = 'ROLE_ETUDIANT';

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getPromotion(): ?string
    {
        return $this->promotion;
    }

    public function setPromotion(string $promotion): static
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function getTD(): ?string
    {
        return $this->TD;
    }

    public function setTD(string $TD): static
    {
        $this->TD = $TD;

        return $this;
    }

    public function getTP(): ?string
    {
        return $this->TP;
    }

    public function setTP(string $TP): static
    {
        $this->TP = $TP;

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
            $note->setEtudiant($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getEtudiant() === $this) {
                $note->setEtudiant(null);
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
            $ficheGrille->setEtudiant($this);
        }

        return $this;
    }

    public function removeFicheGrille(FicheGrille $ficheGrille): static
    {
        if ($this->ficheGrilles->removeElement($ficheGrille)) {
            // set the owning side to null (unless already changed)
            if ($ficheGrille->getEtudiant() === $this) {
                $ficheGrille->setEtudiant(null);
            }
        }

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
            $ficheGroupe->setEtudiant($this);
        }

        return $this;
    }

    public function removeFicheGroupe(FicheGroupe $ficheGroupe): static
    {
        if ($this->ficheGroupes->removeElement($ficheGroupe)) {
            // set the owning side to null (unless already changed)
            if ($ficheGroupe->getEtudiant() === $this) {
                $ficheGroupe->setEtudiant(null);
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
            $ficheCour->setEtudiant($this);
        }

        return $this;
    }

    public function removeFicheCour(FicheCours $ficheCour): static
    {
        if ($this->ficheCours->removeElement($ficheCour)) {
            // set the owning side to null (unless already changed)
            if ($ficheCour->getEtudiant() === $this) {
                $ficheCour->setEtudiant(null);
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
     * @return Collection<int, FicheGroupe>
     */
    public function getFicheGroupe(): Collection
    {
        return $this->Fiche_groupe;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNote(): Collection
    {
        return $this->Note;
    }

}  
