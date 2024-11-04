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

  #[ORM\OneToOne(inversedBy: 'date', cascade: ['persist', 'remove'])]
  private ?Users $user_id = null;

  #[ORM\Column(type: 'datetime')]
  private ?\DateTimeInterface $date = null;

  #[ORM\Column(length: 255)]
  private ?string $time_start = null;

  #[ORM\Column(length: 255)]
  private ?string $time_end = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getUserId(): ?Users
  {
    return $this->user_id;
  }

  public function setUserId(?Users $user_id): static
  {
    $this->user_id = $user_id;

    return $this;
  }

  public function getDate(): ?\DateTimeInterface
  {
    return $this->date;
  }

  public function setDate(?\DateTimeInterface $date): self
  {
    $this->date = $date;

    return $this;
  }

  public function getTimeStart(): ?string
  {
    return $this->time_start;
  }

  public function setTimeStart(string $time_start): static
  {
    $this->time_start = $time_start;

    return $this;
  }

  public function getTimeEnd(): ?string
  {
    return $this->time_end;
  }

  public function setTimeEnd(string $time_end): static
  {
    $this->time_end = $time_end;

    return $this;
  }
}
