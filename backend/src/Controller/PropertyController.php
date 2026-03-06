<?php

namespace App\Controller;

use App\DTO\Property\CreatePropertyDTO;
use App\DTO\Property\UpdatePropertyDTO;
use App\Entity\Property;
use App\Entity\User;
use App\Security\Voter\PropertyVoter;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/v1/properties')]
class PropertyController extends AbstractController
{
    public function __construct(
        private PropertyService $propertyService,
    ) {
    }

    #[Route('', name: 'api_properties_list', methods: ['GET'])]
    public function list(#[CurrentUser] ?User $user): JsonResponse
    {
        if (! $user) {
            return $this->json(['message' => 'Not authenticated'], 401);
        }

        $properties = $this->propertyService->findAllForUser($user);

        return $this->json(array_map(
            fn (Property $property) => $this->propertyService->formatProperty($property),
            $properties
        ));
    }

    #[Route('/{id}', name: 'api_properties_show', methods: ['GET'])]
    public function show(Property $property): JsonResponse
    {
        $this->denyAccessUnlessGranted(PropertyVoter::VIEW, $property);

        return $this->json($this->propertyService->formatProperty($property));
    }

    #[Route('', name: 'api_properties_create', methods: ['POST'])]
    public function create(
        #[CurrentUser] ?User $user,
        #[MapRequestPayload] CreatePropertyDTO $dto,
    ): JsonResponse {
        if (! $user) {
            return $this->json(['message' => 'Not authenticated'], 401);
        }

        $property = $this->propertyService->createProperty($dto, $user);

        return $this->json(
            $this->propertyService->formatProperty($property),
            201
        );
    }

    #[Route('/{id}', name: 'api_properties_update', methods: ['PUT'])]
    public function update(
        Property $property,
        #[MapRequestPayload] UpdatePropertyDTO $dto,
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PropertyVoter::EDIT, $property);

        $updatedProperty = $this->propertyService->updateProperty($property, $dto);

        return $this->json($this->propertyService->formatProperty($updatedProperty));
    }

    #[Route('/{id}', name: 'api_properties_delete', methods: ['DELETE'])]
    public function delete(Property $property): JsonResponse
    {
        $this->denyAccessUnlessGranted(PropertyVoter::DELETE, $property);

        $this->propertyService->deleteProperty($property);

        return $this->json(['message' => 'Property deleted successfully']);
    }

    #[Route('/{id}/photos', name: 'api_properties_upload_photo', methods: ['POST'])]
    public function uploadPhoto(
        Property $property,
        Request $request,
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PropertyVoter::EDIT, $property);

        $file = $request->files->get('photo');

        if (! $file) {
            return $this->json(['message' => 'No file provided'], 400);
        }

        $updatedProperty = $this->propertyService->addPhoto($property, $file);

        return $this->json($this->propertyService->formatProperty($updatedProperty));
    }
}
