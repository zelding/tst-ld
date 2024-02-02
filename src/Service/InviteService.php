<?php

namespace App\Service;

use App\Entity\Invite;
use App\Entity\User;
use App\Exception\AppException;
use App\Model\InviteStatus;
use App\Repository\InviteRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use RuntimeException;

class InviteService
{
    public const int MAX_ITERATION = 30;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly InviteRepository $inviteRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function getUserInviteDataForProfile(User $user): array
    {
        $userData = $this->userRepository->getUserWithInvites($user);

        $returnData = [
            'me'         => $user,
            'friends'    => [],
            'invited'    => [],
            'invited_by' => []
        ];

        if ( empty($userData) ) {
            return $returnData;
        }

        /** @var User $user */
        $user = reset($userData);

        foreach($user->getInvites() as $invitation) {
            if ( $invitation->getStatus() === InviteStatus::ACCEPTED ) {
                $returnData['friends'][] = $invitation->getInvitee();
            }
            else {
                $returnData['invited_by'][] = $invitation->getInvitee();
            }
        }

        foreach($user->getInvited() as $invitation) {
            if ( $invitation->getStatus() === InviteStatus::ACCEPTED ) {
                $returnData['friends'][] = $invitation->getInviter();
            }
            else {
                $returnData['invited'][] = $invitation->getInviter();
            }
        }

        return $returnData;
    }

    /** @throws ORMException|AppException */
    public function invite(User $user, string $userId): Invite
    {
        $otherUser = $this->userRepository->findOneByUsername($userId);

        if ( !$otherUser ) {
            throw new AppException("Missing target connection", 404);
        }

        if ($found = $this->inviteRepository->findExistingBetween($user, $otherUser)) {
            if ( $found->getStatus() === InviteStatus::BLOCKED ) {
                throw new AppException("Blocked connection", 409);
            }

            throw new AppException("Already invited or Invalid target", 406);
        }

        $invite = new Invite($user, $otherUser);

        $invite->setStatus(InviteStatus::SENT);
        $invite->setValidUntil(new DateTimeImmutable());
        $invite->setHash($this->generateInviteHash());

        $this->entityManager->persist($invite);
        $this->entityManager->flush();

        // trigger messenger

        return $invite;
    }

    /** @throws AppException|ORMException */
    public function acceptInvite(User $user, string $hash): Invite
    {
        $now    = new DateTimeImmutable();
        $invite = $this->inviteRepository->findOneByHash($hash);

        if(!$invite
           || $invite->getStatus() !== InviteStatus::SENT
           || $invite->getInvitee()->getUsername() !== $user->getUsername()) {
            throw new AppException("", 403);
        }

        if ( $invite->getValidUntil() < $now ) {
            throw new AppException("Expired", 406);
        }

        $invite->setStatus(InviteStatus::ACCEPTED);
        $this->entityManager->flush();

        // trigger messenger

        return $invite;
    }

    public function rejectInvite(User $user, string $hash): Invite
    {
        $now    = new DateTimeImmutable();
        $invite = $this->inviteRepository->findOneByHash($hash);

        if(!$invite
           || $invite->getStatus() !== InviteStatus::SENT
           || $invite->getInvitee()->getUsername() !== $user->getUsername()) {
            throw new AppException("", 403);
        }

        $invite->setStatus(InviteStatus::REJECTED);
        $this->entityManager->flush();

        // trigger messenger

        return $invite;
    }

    /** @throws AppException|ORMException */
    public function cancelInvite(User $user, string $hash): Invite
    {
        $invite = $this->inviteRepository->findOneByHash($hash);

        if(!$invite
           || $invite->getStatus() !== InviteStatus::SENT
           || $invite->getInviter()->getUsername() !== $user->getUsername()) {
            throw new AppException("", 403);
        }

        $invite->setStatus(InviteStatus::DELETED);
        $this->entityManager->flush();

        // trigger messenger

        return $invite;
    }

    /** @throws RuntimeException|ORMException */
    protected function generateInviteHash(): string
    {
        $counter = 0;
        do {
            $hash   = static::newHash();
            $entity = $this->inviteRepository->findOneByHash($hash);
            $counter++;
        }
        while ($entity && $counter < self::MAX_ITERATION);

        if ( $counter === self::MAX_ITERATION) {
            // this should trigger an SMS or something
            throw new RuntimeException("No new unique hash could be generated");
        }

        return $hash;
    }

    protected static function newHash(): string
    {
        return hash('sha256', microtime(true));
    }
}
