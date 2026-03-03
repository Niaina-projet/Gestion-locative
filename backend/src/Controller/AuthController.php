<?php

namespace App\Controller;

use App\DTO\Auth\UpdateProfileDTO;
use App\Entity\User;
use App\Security\Voter\UserVoter;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private AuthService $authService,
    ) {
    }

    #[Route('/me', name: 'api_auth_me', methods: ['GET'])]
    public function me(#[CurrentUser] ?User $user): JsonResponse
    {
        if (! $user) {
            return $this->json(['message' => 'Not authenticated'], 401);
        }

        return $this->json($this->authService->formatUser($user));
    }

    #[Route('/profile', name: 'api_auth_profile_update', methods: ['PUT'])]
    public function updateProfile(
        #[CurrentUser] ?User $user,
        #[MapRequestPayload] UpdateProfileDTO $dto,
    ): JsonResponse {
        if (! $user) {
            return $this->json(['message' => 'Not authenticated'], 401);
        }

        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

        $updatedUser = $this->authService->updateProfile($user, $dto);

        return $this->json([
            'message' => 'Profile updated successfully',
            'user' => $this->authService->formatUser($updatedUser),
        ]);
    }
}
