<?php

namespace App\Service;

use App\Entity\Token;
use App\Entity\User;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;

class TokenService
{
    private UserRepository $userRepository;

    private TokenRepository $tokenRepository;

    public function createNewToken(User $user): Token
    {

    }

    public function validateToken(User $user): Token
    {

    }
}
