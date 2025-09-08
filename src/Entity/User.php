<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    private ?string $lastName = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas valide')]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire', groups: ['create'])]
    #[Assert\Length(
        min: 8,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères',
        groups: ['create', 'password_update']
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/',
        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre',
        groups: ['create', 'password_update']
    )]
    private ?string $password = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Le code public ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $publicCode = null;

    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: Session::class,
        cascade: ['remove'],
        orphanRemoval: true
    )]
    private Collection $sessions;

    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: Eleve::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $eleves;

    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: EleveImport::class,
        cascade: ['remove'],
        orphanRemoval: true
    )]
    private Collection $eleveImports;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->eleves = new ArrayCollection();
        $this->eleveImports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPublicCode(): ?string
    {
        return $this->publicCode;
    }

    public function setPublicCode(?string $publicCode): self
    {
        $this->publicCode = $publicCode;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si vous stockez des données temporaires sensibles sur l'utilisateur, effacez-les ici
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setUser($this);
        }
        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getUser() === $this) {
                $session->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Eleve>
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addEleve(Eleve $eleve): self
    {
        if (!$this->eleves->contains($eleve)) {
            $this->eleves->add($eleve);
            $eleve->setUser($this);
        }
        return $this;
    }

    public function removeEleve(Eleve $eleve): self
    {
        if ($this->eleves->removeElement($eleve)) {
            // set the owning side to null (unless already changed)
            if ($eleve->getUser() === $this) {
                $eleve->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, EleveImport>
     */
    public function getEleveImports(): Collection
    {
        return $this->eleveImports;
    }

    public function addEleveImport(EleveImport $eleveImport): self
    {
        if (!$this->eleveImports->contains($eleveImport)) {
            $this->eleveImports[] = $eleveImport;
            $eleveImport->setUser($this);
        }
        return $this;
    }

    public function removeEleveImport(EleveImport $eleveImport): self
    {
        if ($this->eleveImports->removeElement($eleveImport)) {
            if ($eleveImport->getUser() === $this) {
                $eleveImport->setUser(null);
            }
        }
        return $this;
    }
    public function __toString(): string
    {
        return (string) $this->email;
    }
}
