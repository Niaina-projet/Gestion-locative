<?php

namespace App\Service;

use App\DTO\Property\CreatePropertyDTO;
use App\DTO\Property\UpdatePropertyDTO;
use App\Entity\Property;
use App\Entity\User;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PropertyService
{
    public function __construct(
        private PropertyRepository $propertyRepository,
        private EntityManagerInterface $em,
        private string $uploadsDir,
    ) {
    }

    /**
     * @return array{data: Property[], total: int}
     */
    public function findWithFilters(
        User $user,
        ?string $type,
        ?string $status,
        ?string $city,
        ?string $search,
        int $page,
        int $limit,
    ): array {
        $owner = in_array('ROLE_ADMIN', $user->getRoles()) ? null : $user;

        return $this->propertyRepository->findWithFilters(
            $owner,
            $type,
            $status,
            $city,
            $search,
            $page,
            $limit,
        );
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

        $property->setUpdatedAt(new \DateTimeImmutable());
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

    public function addPhoto(Property $property, UploadedFile $file): array
    {
        $allowedMimeTypes = ['image/jpeg', 'image/png'];
        if (! in_array($file->getMimeType(), $allowedMimeTypes)) {
            return ['error' => 'Format invalide. Formats acceptés : jpg, png'];
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return ['error' => 'Fichier trop volumineux. Maximum 2MB'];
        }

        $currentPhotos = $property->getPhotos() ?? [];
        if (count($currentPhotos) >= 10) {
            return ['error' => 'Maximum 10 photos par bien'];
        }

        $newFilename = uniqid().'.'.$file->guessExtension();
        $file->move($this->uploadsDir, $newFilename);

        $currentPhotos[] = '/uploads/properties/'.$newFilename;
        $property->setPhotos($currentPhotos);
        $this->em->flush();

        return ['property' => $property];
    }

    public function removePhoto(Property $property, int $photoIndex): array
    {
        $photos = $property->getPhotos() ?? [];

        if (! isset($photos[$photoIndex])) {
            return ['error' => 'Photo introuvable'];
        }

        $filename = basename($photos[$photoIndex]);
        $filepath = $this->uploadsDir.'/'.$filename;

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        array_splice($photos, $photoIndex, 1);
        $property->setPhotos($photos);
        $this->em->flush();

        return ['property' => $property];
    }
}
