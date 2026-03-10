<?php

namespace App\DTO\Property;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePropertyDTO
{
    #[Assert\Length(max: 150, maxMessage: 'Le titre ne peut pas dépasser 150 caractères')]
    public ?string $title = null;

    #[Assert\Choice(
        choices: ['appartement', 'maison', 'bureau', 'commerce'],
        message: 'Type invalide'
    )]
    public ?string $type = null;

    #[Assert\Length(max: 255)]
    public ?string $address = null;

    #[Assert\Length(max: 100)]
    public ?string $city = null;

    #[Assert\Positive(message: 'La surface doit être positive')]
    public ?float $surface = null;

    #[Assert\Positive(message: 'Le nombre de pièces doit être positif')]
    public ?int $rooms = null;

    #[Assert\Positive(message: 'Le loyer doit être positif')]
    public ?int $rentAmount = null;

    #[Assert\Positive(message: 'La caution doit être positive')]
    public ?int $deposit = null;

    #[Assert\Choice(
        choices: ['available', 'occupied', 'maintenance'],
        message: 'Statut invalide'
    )]
    public ?string $status = null;

    public ?string $description = null;
}
