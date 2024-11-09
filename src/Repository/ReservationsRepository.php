<?php

namespace App\Repository;

use App\Entity\Reservations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservations>
 */
class ReservationsRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Reservations::class);
  }

  public function findAll(): array
  {
    return $this->createQueryBuilder('r')
      ->orderBy('r.id', 'ASC')
      ->getQuery()
      ->getResult();
  }

  // Créer une réservation à partir d'un ID de Slot
  public function createReservationFromSlotIdAndUserId($slotId, $userId): Reservations
  {
    try {
      $entityManager = $this->getEntityManager();
      $slot = $entityManager->getRepository('App\Entity\Slots')->findOneById($slotId); // Récupérer le Slot correspondant à l'ID
      if (!$slot) {
        throw new \Exception('Le créneau n\'existe pas');
      }
      // vérifier que l'utilisateur existe
      $user = $entityManager->getRepository('App\Entity\Users')->findOneById($userId);
      if (!$user) {
        throw new \Exception('L\'utilisateur n\'existe pas');
      }
      // vérifier que le créneau est disponible
      if ($slot->getAvailable() !== 'yes') {
        throw new \Exception('Le créneau n\'est pas disponible');
      }

      $reservation = new Reservations(); // Créer une nouvelle réservation
      $reservation->setUser($userId); // Associer l'User à la réservation
      $reservation->setSlot($slot); // Associer le Slot à la réservation
      $entityManager->persist($reservation); // Préparer l'insertion en base de données
      $entityManager->flush(); // Insérer en base de données

      return $reservation;
    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }
  }

  //    /**
  //     * @return Reservations[] Returns an array of Reservations objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('r')
  //            ->andWhere('r.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('r.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Reservations
  //    {
  //        return $this->createQueryBuilder('r')
  //            ->andWhere('r.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
