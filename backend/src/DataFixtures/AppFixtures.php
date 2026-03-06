<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Compte Admin
        $admin = new User();
        $admin->setEmail('admin@gestion-locative.mg');
        $admin->setPassword($this->hasher->hashPassword($admin, 'Admin@1234'));
        $admin->setFirstName('Admin');
        $admin->setLastName('Système');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsActive(true);
        $manager->persist($admin);

        // Compte Manager de test
        $manager1 = new User();
        $manager1->setEmail('manager@gestion-locative.mg');
        $manager1->setPassword($this->hasher->hashPassword($manager1, 'Manager@1234'));
        $manager1->setFirstName('Jean');
        $manager1->setLastName('Rakoto');
        $manager1->setRoles(['ROLE_MANAGER']);
        $manager1->setIsActive(true);
        $manager->persist($manager1);

        $manager->flush();
    }
}
