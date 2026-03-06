<?php

namespace App\DataFixtures;

use App\Entity\Property;
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

        // Biens immobiliers de test
        $properties = [
            [
                'title' => 'Appartement T3 Analakely',
                'type' => 'appartement',
                'address' => '12 Rue de l\'Indépendance',
                'city' => 'Antananarivo',
                'surface' => '75.00',
                'rooms' => 3,
                'rentAmount' => 450000,
                'deposit' => 900000,
                'status' => 'available',
                'description' => 'Bel appartement T3 en centre ville',
            ],
            [
                'title' => 'Maison F4 Ivandry',
                'type' => 'maison',
                'address' => '45 Allée des Flamboyants',
                'city' => 'Antananarivo',
                'surface' => '120.00',
                'rooms' => 4,
                'rentAmount' => 800000,
                'deposit' => 1600000,
                'status' => 'occupied',
                'description' => 'Grande maison avec jardin',
            ],
            [
                'title' => 'Bureau Tsaralalana',
                'type' => 'bureau',
                'address' => '8 Rue Tsaralalana',
                'city' => 'Antananarivo',
                'surface' => '45.00',
                'rooms' => 2,
                'rentAmount' => 350000,
                'deposit' => 700000,
                'status' => 'maintenance',
                'description' => 'Bureau idéal pour petite entreprise',
            ],
        ];

        foreach ($properties as $data) {
            $property = new Property();
            $property->setTitle($data['title']);
            $property->setType($data['type']);
            $property->setAddress($data['address']);
            $property->setCity($data['city']);
            $property->setSurface($data['surface']);
            $property->setRooms($data['rooms']);
            $property->setRentAmount($data['rentAmount']);
            $property->setDeposit($data['deposit']);
            $property->setStatus($data['status']);
            $property->setDescription($data['description']);
            $property->setOwner($manager1);
            $manager->persist($property);
        }

        $manager->flush();
    }
}
