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
      ['Marius', 'ALBERTINI', 'marius.albertini@test.fr', 'ROLE_USER'],
      ['Élina', 'ALMARZA RUIZ', 'elina.almarza_ruiz@test.fr', 'ROLE_USER'],
      ['Jade', 'BASTIEN', 'jade.bastien@test.fr', 'ROLE_USER'],
      ['Léonardo', 'BRUCHON', 'leonardo.bruchon@test.fr', 'ROLE_USER'],
      ['Noam', 'CARISEY', 'noam.carisey@test.fr', 'ROLE_USER'],
      ['Marius', 'CHAMPOD', 'marius.champod@test.fr', 'ROLE_USER'],
      ['Elena', 'CHAUVIN', 'elena.chauvin@test.fr', 'ROLE_USER'],
      ['Gaspard', 'CUINET', 'gaspard.cuinet@test.fr', 'ROLE_USER'],
      ['Maxence', 'CUINET', 'maxence.cuinet@test.fr', 'ROLE_USER'],
      ['Elona', 'DAVID', 'elona.david@test.fr', 'ROLE_USER'],
      ['Quentin', 'DUCROT', 'quentin.ducrot@test.fr', 'ROLE_USER'],
      ['Gabin', 'FORTRYE-DANEZIS', 'gabin.fortrye_danezis@test.fr', 'ROLE_USER'],
      ['Quentin', 'FRANTZ', 'quentin.frantz@test.fr', 'ROLE_USER'],
      ['Nora', 'HASBROUCQ', 'nora.hasbroucq@test.fr', 'ROLE_USER'],
      ['Tilyo', 'HUOT-MARCHAND', 'tilyo.huot_marchand@test.fr', 'ROLE_USER'],
      ['Samson', 'LACAUSTE', 'samson.lacauste@test.fr', 'ROLE_USER'],
      ['Jules', 'LAMBERT', 'jules.lambert@test.fr', 'ROLE_USER'],
      ['Jules', 'LAVERGNE', 'jules.lavergne@test.fr', 'ROLE_USER'],
      ['Shannon', 'LOTTE', 'shannon.lotte@test.fr', 'ROLE_USER'],
      ['Gabrielle', 'MALPESA', 'gabrielle.malpesa@test.fr', 'ROLE_USER'],
      ['Lucas', 'MOYSE', 'lucas.moyse@test.fr', 'ROLE_USER'],
      ['Rozenn', 'PRIGENT', 'rozenn.prigent@test.fr', 'ROLE_USER'],
      ['Lana', 'RAGOT', 'lana.ragot@test.fr', 'ROLE_USER'],
      ['Raphaël', 'ROLAND', 'raphael.roland@test.fr', 'ROLE_USER'],
      ['Merlin', 'SCHMITT-ROCOULET', 'merlin.schmitt_rocoulet@test.fr', 'ROLE_USER'],
      ['Lauryne', 'SEURET', 'lauryne.seuret@test.fr', 'ROLE_USER'],
      ['Inès', 'SOUIKI', 'ines.souiki@test.fr', 'ROLE_USER'],
      ['Lily-Rose', 'VASCONCELOS', 'lily-rose.vasconcelos@test.fr', 'ROLE_USER'],
      ['Admin', 'ADMIN', 'test@test.fr', 'ROLE_ADMIN'],
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
      '2024-11-13 17:40:00',
      '2024-11-13 18:20:00',
      '2024-11-11 17:00:00',
      '2024-11-11 17:20:00',
      '2024-11-11 17:40:00',
      '2024-11-11 18:00:00',
      '2024-11-11 18:20:00',
      '2024-11-11 18:40:00',
      '2024-11-11 19:00:00',
      '2024-11-12 17:00:00',
      '2024-11-12 17:20:00',
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
