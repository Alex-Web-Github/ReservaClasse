<?php

namespace App\Entity;

use App\Repository\SlotRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlotRepository::class)]
class Slot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isBooked = false;

    #[ORM\ManyToOne(targetEntity: DateSession::class, inversedBy: 'slots')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DateSession $dateSession = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\ManyToOne]
    private ?Eleve $eleve = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getIsBooked(): ?string
    {
        return $this->isBooked;
    }

    public function setIsBooked(string $isBooked): static
    {
        $this->isBooked = $isBooked;

        return $this;
    }

    public function getDateSession(): ?DateSession
    {
        return $this->dateSession;
    }

    public function setDateSession(?DateSession $dateSession): static
    {
        $this->dateSession = $dateSession;

        return $this;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): static
    {
        $this->eleve = $eleve;
        $this->isBooked = ($eleve !== null);

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s-%s%s',
            $this->startTime?->format('H:i') ?? 'N/A',
            $this->endTime?->format('H:i') ?? 'N/A',
            $this->isBooked ? ' (Réservé)' : ''
        );
    }
}
