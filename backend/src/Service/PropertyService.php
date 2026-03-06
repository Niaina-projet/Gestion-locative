<?php

namespace App\Service;

use App\DTO\Property\CreatePropertyDTO;
use App\DTO\Property\UpdatePropertyDTO;
use App\Entity\Property;
use App\Entity\User;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;

class PropertyService
{
    public function __construct(
        private PropertyRepository $propertyRepository,
        private EntityManagerInterface $em,
        private string $uploadsDir = '%kernel.project_dir%/public/uploads/properties',
    ) {
    }

    /**
     * @return Property[]
     */
    public function findAllForUser(User $user): array
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->propertyRepository->findAll();
        }

        return $this->propertyRepository->findByOwner($user);
    }

    public function createProperty(CreatePropertyDTO $dto, User $owner): Property
    {
        $property = new Property();
        $property->setTitle($dto->title);
        $property->setType($dto->type);
        $property->setAddress($dto->address);
        $property->setCity($dto->city);
        $property->setRentAmount($dto->rentAmount);
        $property->setOwner($owner);

        if (null !== $dto->surface) {
            $property->setSurface((string) $dto->surface);
        }

        if (null !== $dto->rooms) {
            $property->setRooms($dto->rooms);
        }

        if (null !== $dto->deposit) {
            $property->setDeposit($dto->deposit);
        }

        if (null !== $dto->description) {
            $property->setDescription($dto->description);
        }

        $this->em->persist($property);
        $this->em->flush();

        return $property;
    }

    public function updateProperty(Property $property, UpdatePropertyDTO $dto): Property
    {
        if (null !== $dto->title) {
            $property->setTitle($dto->title);
        }

        if (null !== $dto->type) {
            $property->setType($dto->type);
        }

        if (null !== $dto->address) {
            $property->setAddress($dto->address);
        }

        if (null !== $dto->city) {
            $property->setCity($dto->city);
        }

        if (null !== $dto->rentAmount) {
            $property->setRentAmount($dto->rentAmount);
        }

        if (null !== $dto->deposit) {
            $property->setDeposit($dto->deposit);
        }

        if (null !== $dto->surface) {
            $property->setSurface((string) $dto->surface);
        }

        if (null !== $dto->rooms) {
            $property->setRooms($dto->rooms);
        }

        if (null !== $dto->status) {
            $property->setStatus($dto->status);
        }

        if (null !== $dto->description) {
            $property->setDescription($dto->description);
        }

        $this->em->flush();

        return $property;
    }

    public function deleteProperty(Property $property): void
    {
        $this->em->remove($property);
        $this->em->flush();
    }

    public function formatProperty(Property $property): array
    {
        return [
            'id' => $property->getId(),
            'title' => $property->getTitle(),
            'type' => $property->getType(),
            'address' => $property->getAddress(),
            'city' => $property->getCity(),
            'surface' => $property->getSurface(),
            'rooms' => $property->getRooms(),
            'rentAmount' => $property->getRentAmount(),
            'deposit' => $property->getDeposit(),
            'status' => $property->getStatus(),
            'description' => $property->getDescription(),
            'photos' => $property->getPhotos() ?? [],
            'createdAt' => $property->getCreatedAt()->format('Y-m-d H:i:s'),
            'owner' => [
                'id' => $property->getOwner()->getId(),
                'firstName' => $property->getOwner()->getFirstName(),
                'lastName' => $property->getOwner()->getLastName(),
            ],
        ];
    }

    public function addPhoto(Property $property, \Symfony\Component\HttpFoundation\File\UploadedFile $file): Property
    {
        $uploadsDir = $this->uploadsDir;
        $newFilename = uniqid().'.'.$file->guessExtension();
        $file->move($uploadsDir, $newFilename);

        $photos = $property->getPhotos() ?? [];
        $photos[] = '/uploads/properties/'.$newFilename;
        $property->setPhotos($photos);

        $this->em->flush();

        return $property;
    }
}
