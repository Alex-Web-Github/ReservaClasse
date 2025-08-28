<?php

namespace App\Entity;

use App\Entity\Slot;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
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

    #[ORM\OneToMany(mappedBy: 'dateSession', targetEntity: Slot::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $slots;

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
        $this->slots = new ArrayCollection();
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
        $this->recalculateSlots();
        return $this;
    }

    public function getEndTime(): \DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;
        $this->recalculateSlots();
        return $this;
    }

    public function recalculateSlots(): void
    {
        // Vérifier les conditions requises
        if (!$this->session || !$this->startTime || !$this->endTime) {
            return;
        }

        // Convertir les heures en minutes depuis minuit pour faciliter les calculs
        $startMinutes = $this->startTime->format('H') * 60 + $this->startTime->format('i');
        $endMinutes = $this->endTime->format('H') * 60 + $this->endTime->format('i');

        if ($startMinutes >= $endMinutes) {
            return;
        }

        $slotDuration = $this->session->getSlotDuration();
        $slotInterval = $this->session->getSlotInterval();

        // Limiter le nombre maximum de créneaux pour éviter les boucles infinies
        $maxSlots = 50; // Nombre maximum raisonnable de créneaux
        $slotsCount = 0;

        // Supprimer les créneaux existants
        $this->slots->clear();

        $currentMinutes = $startMinutes;

        while ($currentMinutes < $endMinutes && $slotsCount < $maxSlots) {
            $slotEndMinutes = $currentMinutes + $slotDuration;

            // Vérifier si le créneau ne dépasse pas l'heure de fin
            if ($slotEndMinutes <= $endMinutes) {
                $slot = new Slot();
                $slot->setDateSession($this);

                // Créer les DateTime pour le début et la fin du créneau
                $slotStart = new \DateTime($this->date->format('Y-m-d'));
                $slotStart->setTime(intdiv($currentMinutes, 60), $currentMinutes % 60);

                $slotEnd = new \DateTime($this->date->format('Y-m-d'));
                $slotEnd->setTime(intdiv($slotEndMinutes, 60), $slotEndMinutes % 60);

                $slot->setStartTime($slotStart);
                $slot->setEndTime($slotEnd);

                $this->slots->add($slot);
                $slotsCount++;
            }

            // Passer au prochain créneau
            $currentMinutes += $slotDuration + $slotInterval;
        }
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

    /**
     * @return Collection<int, Slot>
     */
    public function getSlots(): Collection
    {
        return $this->slots;
    }

    public function addSlot(Slot $slot): self
    {
        if (!$this->slots->contains($slot)) {
            $this->slots->add($slot);
            $slot->setDateSession($this);
        }
        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %s-%s',
            $this->date->format('d/m/Y'),
            $this->startTime->format('H:i'),
            $this->endTime->format('H:i')
        );
    }

    public function removeSlot(Slot $slot): self
    {
        if ($this->slots->removeElement($slot)) {
            if ($slot->getDateSession() === $this) {
                $slot->setDateSession(null);
            }
        }

        return $this;
    }
}
