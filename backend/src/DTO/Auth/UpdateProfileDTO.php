<?php

namespace App\DTO\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateProfileDTO
{
    #[Assert\NotBlank(message: 'First name is required')]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $firstName = null;

    #[Assert\NotBlank(message: 'Last name is required')]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $lastName = null;

    #[Assert\Length(min: 8, minMessage: 'Password must be at least 8 characters')]
    public ?string $password = null;
}
