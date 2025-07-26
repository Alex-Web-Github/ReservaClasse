<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column]
    private ?int $slotDuration = null;

    #[ORM\Column]
    private ?int $slotInterval = null;

    #[ORM\Column(length: 20)]
    private ?string $publicCode = null;

    #[ORM\Column(length: 20)]
    private ?string $parentCode = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getSlotDuration(): ?int
    {
        return $this->slotDuration;
    }

    public function setSlotDuration(int $slotDuration): static
    {
        $this->slotDuration = $slotDuration;

        return $this;
    }

    public function getSlotInterval(): ?int
    {
        return $this->slotInterval;
    }

    public function setSlotInterval(int $slotInterval): static
    {
        $this->slotInterval = $slotInterval;

        return $this;
    }

    public function getPublicCode(): ?string
    {
        return $this->publicCode;
    }

    public function setPublicCode(string $publicCode): static
    {
        $this->publicCode = $publicCode;

        return $this;
    }

    public function getParentCode(): ?string
    {
        return $this->parentCode;
    }

    public function setParentCode(string $parentCode): static
    {
        $this->parentCode = $parentCode;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
