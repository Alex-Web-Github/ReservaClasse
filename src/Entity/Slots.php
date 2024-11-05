<?php

namespace App\Entity;

use App\Repository\SlotsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlotsRepository::class)]
class Slots
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(type: Types::DATE_IMMUTABLE)]
  private ?\DateTimeImmutable $date = null;

  #[ORM\Column(type: Types::TIME_IMMUTABLE)]
  private ?\DateTimeImmutable $time_start = null;

  #[ORM\Column(length: 255)]
  private ?string $available = null;


  public function getId(): ?int
  {
    return $this->id;
  }

  public function getDate(): ?\DateTimeImmutable
  {
    return $this->date;
  }

  public function setDate(\DateTimeImmutable $date): static
  {
    $this->date = $date;

    return $this;
  }

  public function getTimeStart(): ?\DateTimeImmutable
  {
    return $this->time_start;
  }

  public function setTimeStart(\DateTimeImmutable $time_start): static
  {
    $this->time_start = $time_start;

    return $this;
  }

  public function getAvailable(): ?string
  {
    return $this->available;
  }

  public function setAvailable(string $available): static
  {
    $this->available = $available;

    return $this;
  }
}
