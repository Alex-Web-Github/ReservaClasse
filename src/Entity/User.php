<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $adminCode = null;

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

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
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

    public function getAdminCode(): ?string
    {
        return $this->adminCode;
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
    public function setAdminCode(?string $adminCode): self
    {
        $this->adminCode = $adminCode;
        return $this;
    }
}
