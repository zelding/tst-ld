<?php

namespace App\Service;

use App\Entity\Token;
use Symfony\Component\Security\Core\User\UserInterface;

class InviteService
{
    public function __construct(private TokenService $tokenService) {}

    public function invite(UserInterface $user, UserInterface $suer)
    {
        $this->tokenService->createNewToken($user);
    }

    public function acceptInvite(UserInterface $user, Token $token)
    {

    }

    public function cancelInvite(UserInterface $user, Token $token)
    {

    }

    public function denyInvite(UserInterface $user, Token $token)
    {

    }
}
