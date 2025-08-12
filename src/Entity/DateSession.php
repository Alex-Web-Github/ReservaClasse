<?php

namespace App\Entity;

use App\Entity\Slot;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DateSessionRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DateSessionRepository::class)]
class DateSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull(message: 'La date est obligatoire')]
    #[Assert\GreaterThanOrEqual('today', message: 'La date doit être aujourd\'hui ou future')]
    private \DateTimeInterface $date;

    #[ORM\Column(type: 'time')]
    #[Assert\NotNull(message: 'L\'heure de début est obligatoire')]
    private \DateTimeInterface $startTime;

    #[ORM\Column(type: 'time')]
    #[Assert\NotNull(message: 'L\'heure de fin est obligatoire')]
    #[Assert\GreaterThan(propertyPath: 'startTime', message: 'L\'heure de fin doit être après l\'heure de début')]
    private \DateTimeInterface $endTime;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'dates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Session $session = null;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->startTime = new \DateTime('16:30');
        $this->endTime = new \DateTime('18:30');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): \DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }
    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;
        return $this;
    }
}
