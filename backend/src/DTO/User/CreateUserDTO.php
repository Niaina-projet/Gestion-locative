<?php

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDTO
{
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Invalid email format')]
    public ?string $email = null;

    #[Assert\Length(min: 8, minMessage: 'Password must be at least 8 characters')]
    public ?string $password = null;

    #[Assert\NotBlank(message: 'First name is required')]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $firstName = null;

    #[Assert\NotBlank(message: 'Last name is required')]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $lastName = null;

    #[Assert\Choice(
        choices: ['ROLE_ADMIN', 'ROLE_MANAGER'],
        message: 'Invalid role'
    )]
    public string $role = 'ROLE_MANAGER';
}
