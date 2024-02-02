<?php

namespace App\Service;

use App\Entity\AuthToken;
use App\Entity\Invite;
use App\Entity\User;
use App\Model\InviteStatus;
use App\Repository\AuthTokenRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class AuthTokenService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly AuthTokenRepository    $tokenRepository)
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function authenticate(User $user): AuthToken
    {
        $token = $this->tokenRepository->findActiveToken($user, null);

        if($token) {
            return $token;
        }

        return $this->createNewToken($user);
    }

    public function createNewToken(User $user): AuthToken
    {
        $token = new AuthToken();
        $token->setUser($user);
        $token->setValidUntil((new DateTimeImmutable())->modify('+1 hour'));
        $token->setToken($this->generateUniqueHash());

        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return $token;
    }

    public function validateToken(User $user, string $token ): bool
    {
        return null !== $this->tokenRepository->findActiveToken($user, $token);
    }

    protected function generateUniqueHash(): string
    {
        return hash('haval160,4', uniqid("", true));
    }
}
