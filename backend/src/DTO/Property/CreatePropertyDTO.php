<?php

namespace App\DTO\Property;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePropertyDTO
{
    #[Assert\NotBlank(message: 'Le titre est requis')]
    #[Assert\Length(max: 150, maxMessage: 'Le titre ne peut pas dépasser 150 caractères')]
    public ?string $title = null;

    #[Assert\NotBlank(message: 'Le type est requis')]
    #[Assert\Choice(
        choices: ['appartement', 'maison', 'bureau', 'commerce'],
        message: 'Type invalide'
    )]
    public ?string $type = null;

    #[Assert\NotBlank(message: "L'adresse est requise")]
    #[Assert\Length(max: 255)]
    public ?string $address = null;

    #[Assert\NotBlank(message: 'La ville est requise')]
    #[Assert\Length(max: 100)]
    public ?string $city = null;

    #[Assert\Positive(message: 'La surface doit être positive')]
    public ?float $surface = null;

    #[Assert\Positive(message: 'Le nombre de pièces doit être positif')]
    public ?int $rooms = null;

    #[Assert\NotBlank(message: 'Le loyer est requis')]
    #[Assert\Positive(message: 'Le loyer doit être positif')]
    public ?int $rentAmount = null;

    #[Assert\Positive(message: 'La caution doit être positive')]
    public ?int $deposit = null;

    public ?string $description = null;
}
