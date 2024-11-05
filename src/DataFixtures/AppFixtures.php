<?php

namespace App\DataFixtures;

use App\Entity\Slots;
use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

// RAPPEL: pour MAJ la BDD : symfony console doctrine:fixtures:load -n

class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    $this->loadUsers($manager);
    $this->loadSlots($manager);
  }


  private function loadUsers(ObjectManager $manager): void
  {
    $users = [
      ['John', 'Doe', 'john.doe@gmail.com', 'ROLE_USER'],
      ['Jane', 'Doe', 'jane.doe@gmail.com', 'ROLE_USER'],
      ['Admin', 'Admin', 'admin@gmail.com', 'ROLE_ADMIN'],  // pour tester la page Admin
    ];

    foreach ($users as $userData) {
      $user = new Users();
      $user->setFirstname($userData[0]);
      $user->setLastname($userData[1]);
      $user->setEmail($userData[2]);
      $user->setRole($userData[3]);
      $manager->persist($user);
    }

    $manager->flush();
  }

  private function loadSlots(ObjectManager $manager): void
  {
    $slots = [
      '2024-11-11 17:00:00',
      '2024-11-11 17:20:00',
      '2024-11-11 17:40:00',
      '2024-11-11 18:00:00',
      '2024-11-11 18:20:00',
      '2024-11-11 18:40:00',
      '2024-11-11 19:00:00',
      '2024-11-12 17:00:00',
      '2024-11-12 17:20:00'
    ];

    // Ajoutez d'autres créneaux horaires ici
    foreach ($slots as $slotData) {
      $slot = new Slots();
      $slot->setDateTime(new \DateTimeImmutable($slotData));
      $slot->setAvailable('yes');
      $manager->persist($slot);
    }

    $manager->flush();
  }
}
