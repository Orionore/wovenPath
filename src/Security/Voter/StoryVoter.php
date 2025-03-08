<?php

namespace App\Security\Voter;

use App\Entity\Story;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StoryVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['view', 'edit', 'delete'])
            && $subject instanceof Story;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Story $story */
        $story = $subject;

        return match($attribute) {
            'view' => $this->canView($story, $user),
            'edit', 'delete' => $this->canManage($story, $user),
            default => false,
        };
    }

    private function canView(Story $story, User $user): bool
    {
        if ($story->isStatus()) {
            return true;
        }

        return $this->canManage($story, $user);
    }

    private function canManage(Story $story, User $user): bool
    {
        return $story->getUserId() === $user->getId();
    }
}