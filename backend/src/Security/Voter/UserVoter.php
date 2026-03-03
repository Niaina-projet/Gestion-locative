<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const VIEW = 'USER_VIEW';
    public const EDIT = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (! $currentUser instanceof User) {
            return false;
        }

        /* @var User $subject */
        return match ($attribute) {
            self::VIEW => $this->canView($subject, $currentUser),
            self::EDIT => $this->canEdit($subject, $currentUser),
            self::DELETE => $this->canDelete($currentUser),
            default => false,
        };
    }

    private function canView(User $subject, User $currentUser): bool
    {
        // Admin peut voir tous les users
        if (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return true;
        }

        // Manager peut voir uniquement son propre profil
        return $subject->getId() === $currentUser->getId();
    }

    private function canEdit(User $subject, User $currentUser): bool
    {
        // Admin peut modifier tous les users
        if (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return true;
        }

        // Manager peut modifier uniquement son propre profil
        return $subject->getId() === $currentUser->getId();
    }

    private function canDelete(User $currentUser): bool
    {
        // Seul l'admin peut supprimer
        return in_array('ROLE_ADMIN', $currentUser->getRoles());
    }
}
