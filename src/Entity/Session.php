<?php

namespace App\Entity;

use Assert\NotNull;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le label est obligatoire')]
    private string $label;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $teacher = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\Positive(message: 'La durée doit être positive')]
    private int $slotDuration;

    #[ORM\Column(type: 'integer')]
    #[Assert\PositiveOrZero(message: 'L\'intervalle doit être positif ou nul')]
    private int $slotInterval;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: DateSession::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $dates;

    public function __construct()
    {
        $this->dates = new ArrayCollection();
        $this->slotDuration = 20;        // 20 minutes par défaut
        $this->slotInterval = 0;         // pas d'intervalle par défaut
        $this->label = '';               // label vide par défaut
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): self
    {
        $this->teacher = $teacher;
        return $this;
    }

    public function getSlotDuration(): int
    {
        return $this->slotDuration;
    }

    public function setSlotDuration(int $slotDuration): self
    {
        $this->slotDuration = $slotDuration;
        return $this;
    }

    public function getSlotInterval(): int
    {
        return $this->slotInterval;
    }

    public function setSlotInterval(int $slotInterval): self
    {
        $this->slotInterval = $slotInterval;
        return $this;
    }

    /**
     * @return Collection<int, DateSession>
     */
    public function getDates(): Collection
    {
        return $this->dates;
    }

    public function addDate(DateSession $date): self
    {
        if (!$this->dates->contains($date)) {
            $this->dates->add($date);
            $date->setSession($this);
        }
        return $this;
    }

    public function removeDate(DateSession $date): self
    {
        if ($this->dates->removeElement($date)) {
            if ($date->getSession() === $this) {
                $date->setSession(null);
            }
        }
        return $this;
    }
}
