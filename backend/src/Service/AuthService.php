<?php

namespace App\Service;

use App\DTO\Auth\UpdateProfileDTO;
use App\Entity\User;
use App\Trait\UserFormatterTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    use UserFormatterTrait;

    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function updateProfile(User $user, UpdateProfileDTO $dto): User
    {
        if (null !== $dto->firstName) {
            $user->setFirstName($dto->firstName);
        }

        if (null !== $dto->lastName) {
            $user->setLastName($dto->lastName);
        }

        if (null !== $dto->password) {
            $user->setPassword($this->hasher->hashPassword($user, $dto->password));
        }

        $this->em->flush();

        return $user;
    }
}
