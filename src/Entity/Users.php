<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UsersRepository;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $firstname = null;

  #[ORM\Column(length: 255)]
  private ?string $lastname = null;

  #[ORM\Column(length: 255, unique: true)]
  private ?string $email = null;

  #[ORM\Column(length: 50)]
  private ?string $role = null;

  #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reservations::class, cascade: ['persist', 'remove'])]
  private Collection $reservations;

  public function __construct()
  {
    $this->reservations = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getFirstname(): ?string
  {
    return $this->firstname;
  }
  public function setFirstname(string $firstname): self
  {
    $this->firstname = $firstname;
    return $this;
  }

  public function getLastname(): ?string
  {
    return $this->lastname;
  }
  public function setLastname(string $lastname): self
  {
    $this->lastname = $lastname;
    return $this;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }
  public function setEmail(string $email): self
  {
    $this->email = $email;
    return $this;
  }

  public function getRole(): ?string
  {
    return $this->role;
  }
  public function setRole(string $role): self
  {
    $this->role = $role;
    return $this;
  }

  /**
   * @return Collection|Reservations[]
   */
  public function getReservations(): Collection
  {
    return $this->reservations;
  }

  public function addReservation(Reservations $reservation): self
  {
    if (!$this->reservations->contains($reservation)) {
      $this->reservations[] = $reservation;
      $reservation->setUser($this);
    }

    return $this;
  }

  public function removeReservation(Reservations $reservation): self
  {
    if ($this->reservations->removeElement($reservation)) {
      // set the owning side to null (unless already changed)
      // if ($reservation->getUser() === $this) {
      //   $reservation->setUser(null);
      // }
    }

    return $this;
  }
}
