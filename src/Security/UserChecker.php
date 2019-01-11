<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 11/01/2019
 * Time: 20:05
 */

namespace App\Security;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\AppUser as AppUser;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;


class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {

    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof AppUser) {
            return;
        }

        // user account is expired, the user may be notified
        if ($user->getDeactivated()) {
            throw new AccountExpiredException('');
        }
    }
}