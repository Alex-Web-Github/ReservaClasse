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
  // Utilisation de la transaction SQL pour garantir l'intégrité des données au cas où 2 utilisateurs essaient de réserver le même créneau en même temps
  public function createReservationFromSlotIdAndUserId($slotId, $userId): Reservations
  {
    $entityManager = $this->getEntityManager();
    $entityManager->beginTransaction(); // Démarre une transaction SQL

    try {
      // vérifier que le créneau existe
      $slot = $entityManager->getRepository('App\Entity\Slots')->findOneById($slotId);
      if (!$slot) {
        throw new \Exception('Le créneau n\'existe pas');
      }

      // vérifier que le créneau est disponible
      if ($slot->getAvailable() !== 'yes') {
        throw new \Exception('Le créneau n\'est pas disponible');
      }

      // vérifier que l'utilisateur existe
      $user = $entityManager->getRepository('App\Entity\Users')->findOneById($userId);
      if (!$user) {
        throw new \Exception('L\'utilisateur n\'existe pas');
      }

      // vérifier que l'utilisateur n'a pas déjà une réservation pour ce créneau
      $existingReservation = $entityManager->getRepository('App\Entity\Reservations')->findOneBy([
        'user' => $user,
        'slot' => $slot
      ]);
      if ($existingReservation) {
        throw new \Exception('L\'utilisateur a déjà une réservation pour ce créneau');
      }

      // Créer la réservation et mettre à jour le statut du créneau
      $reservation = new Reservations();
      $reservation->setUser($user);
      $reservation->setSlot($slot);

      $slot->setAvailable('no');

      $entityManager->persist($reservation);
      $entityManager->persist($slot);
      $entityManager->flush();

      $entityManager->commit(); // Valide toutes les opérations de la transaction

      // Pour afficher les infos de la réservation dans la vue (affichage du contenu de la réservation effectuée par exe.)
      return $reservation;
    } catch (\Exception $e) {
      $entityManager->rollback(); // Annule toutes les modifications en cas d'erreur
      throw new \Exception($e->getMessage()); //TODO: afficher un message d'erreur à l'utilisateur
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
