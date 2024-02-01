<?php

namespace App\Service;

use App\Entity\Token;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly TokenRepository $tokenRepository)
    {
    }

    public function createNewToken(UserInterface $user): Token
    {
        $user = $this->userRepository->find($user->getUserIdentifier());

        $token = new Token();
        $token->setUser($user);
        $token->setValidUntil(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', 'now'));
        $token->setHash($this->generateUniqueHash());

        $this->entityManager->persist($token);
        $this->entityManager->flush();
    }

    public function validateToken(UserInterface $user, Token $token ): bool
    {

    }

    public function useToken(UserInterface $user, Token $token)
    {

        $this->entityManager->flush();
    }

    protected function generateUniqueHash(): string
    {
        return hash('haval160,4', uniqid("", true));
    }
}
