<?php

namespace App\Security\Voter;

use App\Entity\Property;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PropertyVoter extends Voter
{
    public const VIEW = 'PROPERTY_VIEW';
    public const EDIT = 'PROPERTY_EDIT';
    public const DELETE = 'PROPERTY_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Property;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (! $currentUser instanceof User) {
            return false;
        }

        /* @var Property $subject */
        return match ($attribute) {
            self::VIEW => $this->canView($subject, $currentUser),
            self::EDIT => $this->canEdit($subject, $currentUser),
            self::DELETE => $this->canDelete($subject, $currentUser),
            default => false,
        };
    }

    private function canView(Property $property, User $currentUser): bool
    {
        if (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return true;
        }

        return $property->getOwner()->getId() === $currentUser->getId();
    }

    private function canEdit(Property $property, User $currentUser): bool
    {
        if (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return true;
        }

        return $property->getOwner()->getId() === $currentUser->getId();
    }

    private function canDelete(Property $property, User $currentUser): bool
    {
        return in_array('ROLE_ADMIN', $currentUser->getRoles());
    }
}
