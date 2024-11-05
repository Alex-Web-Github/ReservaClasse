<?php

namespace App\Entity;

use App\Repository\ReservationsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationsRepository::class)]
class Reservations
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\OneToOne(cascade: ['persist', 'remove'])]
  private ?Users $user = null;

  #[ORM\OneToOne(cascade: ['persist', 'remove'])]
  private ?Slots $slot = null;

  #[ORM\Column(length: 255)]
  private ?string $status = 'pending';

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getUser(): ?Users
  {
    return $this->user;
  }

  public function setUser(?Users $user): static
  {
    $this->user = $user;

    return $this;
  }

  public function getSlot(): ?Slots
  {
    return $this->slot;
  }

  public function setSlot(?Slots $slot): static
  {
    $this->slot = $slot;

    return $this;
  }

  public function getStatus(): ?string
  {
    return $this->status;
  }

  public function setStatus(string $status): static
  {
    $this->status = $status;

    return $this;
  }
}
