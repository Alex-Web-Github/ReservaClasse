<?php

namespace App\Service;

use App\Entity\Slot;
use App\Entity\DateSession;
use Doctrine\ORM\EntityManagerInterface;

class SlotGenerator
{
    public function __construct(private EntityManagerInterface $em) {}

    public function generateForDateSession(DateSession $dateSession, int $slotDuration, int $slotInterval): void
    {
        $start = $dateSession->getStartTime();
        $end = $dateSession->getEndTime();

        // Vérification du type
        if (!$start instanceof \DateTime && !$start instanceof \DateTimeImmutable) {
            throw new \InvalidArgumentException('StartTime doit être un DateTime ou DateTimeImmutable');
        }

        while ($start < $end) {
            $next = $start instanceof \DateTimeImmutable
                ? $start->modify("+{$slotDuration} minutes")
                : (clone $start)->modify("+{$slotDuration} minutes");

            if ($next > $end) break;

            $slot = new Slot();
            $slot->setDateSession($dateSession);
            $slot->setStartTime($start);
            $slot->setEndTime($next);
            $slot->setIsBooked(false);

            $this->em->persist($slot);

            // Ajouter l'intervalle entre les créneaux
            $start = $next instanceof \DateTimeImmutable
                ? $next->modify("+{$slotInterval} minutes")
                : $next->modify("+{$slotInterval} minutes");
        }
    }
}
