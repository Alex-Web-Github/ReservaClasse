<?php

namespace App\DataFixtures;

use App\Entity\Slots;
use App\Entity\Users;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

// RAPPEL: pour MAJ la BDD : symfony console doctrine:fixtures:load -n
class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    $user1 = new Users();
    $user1->setFirstname('John');
    $user1->setLastname('Doe');
    $user1->setEmail('john.doe@gmail.com');
    $user1->setRole('ROLE_USER');
    // $user1->setPassword('123456'); // à voir plus tard si besoin - ne pas faire en Prod !
    $manager->persist($user1);

    $user2 = new Users();
    $user2->setFirstname('Jane');
    $user2->setLastname('Doe');
    $user2->setEmail('jane.doe@gmail.com');
    $user2->setRole('ROLE_USER');
    // $user2>setPassword('123456'); // à voir plus tard si besoin - ne pas faire en Prod !
    $manager->persist($user2);

    $slot1 = new Slots();
    $slot1->setDate(new \DateTimeImmutable('2024-11-14'));
    $slot1->setTimeStart(new \DateTimeImmutable('2024-11-14 17:00:00'));
    $manager->persist($slot1);

    $slot2 = new Slots();
    $slot2->setDate(new \DateTimeImmutable('2024-11-15'));
    $slot2->setTimeStart(new \DateTimeImmutable('18:20:00'));
    $manager->persist($slot2);

    $slot3 = new Slots();
    $slot3->setDate(new \DateTimeImmutable('2024-11-16'));
    $slot3->setTimeStart(new \DateTimeImmutable('19:40:00'));
    $manager->persist($slot3);

    $manager->flush();
  }
}
