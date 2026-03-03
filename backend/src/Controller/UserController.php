<?php

namespace App\Controller;

use App\DTO\User\CreateUserDTO;
use App\DTO\User\UpdateUserDTO;
use App\Entity\User;
use App\Security\Voter\UserVoter;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/users')]
class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
    ) {
    }

    #[Route('', name: 'api_users_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->userService->findAll();

        return $this->json(array_map(
            fn (User $user) => $this->userService->formatUser($user),
            $users
        ));
    }

    #[Route('', name: 'api_users_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateUserDTO $dto,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->userService->emailExists($dto->email)) {
            return $this->json(['message' => 'Email already exists'], 409);
        }

        $user = $this->userService->createUser($dto);

        return $this->json([
            'message' => 'User created successfully',
            'user' => $this->userService->formatUser($user),
        ], 201);
    }

    #[Route('/{id}', name: 'api_users_update', methods: ['PUT'])]
    public function update(
        User $user,
        #[MapRequestPayload] UpdateUserDTO $dto,
    ): JsonResponse {
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

        $updatedUser = $this->userService->updateUser($user, $dto);

        return $this->json([
            'message' => 'User updated successfully',
            'user' => $this->userService->formatUser($updatedUser),
        ]);
    }

    #[Route('/{id}/toggle', name: 'api_users_toggle', methods: ['PATCH'])]
    public function toggle(User $user): JsonResponse
    {
        $this->denyAccessUnlessGranted(UserVoter::DELETE, $user);

        $this->userService->toggleUser($user);

        return $this->json([
            'message' => 'User status updated',
            'isActive' => $user->isActive(),
        ]);
    }
}
