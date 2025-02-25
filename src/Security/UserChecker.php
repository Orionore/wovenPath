<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            throw new CustomUserMessageAuthenticationException(
                'Vous n\'avez pas les autorisations requises pour vous connecter.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            throw new CustomUserMessageAuthenticationException(
                'Vous n\'avez pas les autorisations requises pour vous connecter.'
            );
        }
    }
}