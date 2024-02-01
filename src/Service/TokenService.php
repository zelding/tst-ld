<?php

namespace App\Service;

use App\Entity\Invite;
use App\Entity\User;
use App\Model\InviteStatus;
use App\Repository\InviteRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly InviteRepository       $tokenRepository)
    {
    }

    public function createNewToken(User $user, User $otherUser): Invite
    {
        $token = new Invite();
        $token->setInviter($user);
        $token->setInvitee($otherUser);
        $token->setStatus(InviteStatus::INIT);
        $token->setValidUntil(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', 'now'));
        $token->setHash($this->generateUniqueHash());

        $this->entityManager->persist($token);
        $this->entityManager->flush();
    }

    public function validateToken(UserInterface $user, Invite $token ): bool
    {

    }

    public function useToken(UserInterface $user, Invite $token)
    {

        $this->entityManager->flush();
    }

    protected function generateUniqueHash(): string
    {
        return hash('haval160,4', uniqid("", true));
    }
}
