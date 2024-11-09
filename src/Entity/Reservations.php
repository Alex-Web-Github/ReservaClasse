<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationsRepository;

#[ORM\Entity(repositoryClass: ReservationsRepository::class)]
class Reservations
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'reservations')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Users $user = null;

  #[ORM\OneToOne(targetEntity: Slots::class, cascade: ['persist', 'remove'])]
  #[ORM\JoinColumn(nullable: false)]
  private ?Slots $slot = null;

  #[ORM\Column(length: 255)]
  private ?string $status = 'confirmé';

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getUser(): ?Users
  {
    return $this->user;
  }
  public function setUser(Users $user): self
  {
    $this->user = $user;
    return $this;
  }

  public function getSlot(): ?Slots
  {
    return $this->slot;
  }
  public function setSlot(Slots $slot): self
  {
    $this->slot = $slot;
    return $this;
  }

  public function getStatus(): ?string
  {
    return $this->status;
  }
  public function setStatus(string $status): self
  {
    $this->status = $status;
    return $this;
  }

  // Calcul heure de fin de rendez-vous (20 minutes) à partir
  // de l'heure de début
  public function getEndTime(): ?\DateTimeImmutable
  {
    return $this->slot->getDateTime()->modify('+20 minutes');
  }
}
