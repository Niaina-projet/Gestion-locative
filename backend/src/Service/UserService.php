<?php

namespace App\Service;

use App\DTO\User\CreateUserDTO;
use App\DTO\User\UpdateUserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Trait\UserFormatterTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    use UserFormatterTrait;

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function createUser(CreateUserDTO $dto): User
    {
        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword($this->hasher->hashPassword($user, $dto->password));
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);
        $user->setRoles([$dto->role]);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function updateUser(User $user, UpdateUserDTO $dto): User
    {
        if (null !== $dto->firstName) {
            $user->setFirstName($dto->firstName);
        }

        if (null !== $dto->lastName) {
            $user->setLastName($dto->lastName);
        }

        if (null !== $dto->role) {
            $user->setRoles([$dto->role]);
        }

        if (null !== $dto->password) {
            $user->setPassword($this->hasher->hashPassword($user, $dto->password));
        }

        $this->em->flush();

        return $user;
    }

    public function toggleUser(User $user): User
    {
        $user->setIsActive(! $user->isActive());
        $this->em->flush();

        return $user;
    }

    public function emailExists(string $email): bool
    {
        return null !== $this->userRepository->findByEmail($email);
    }
}
