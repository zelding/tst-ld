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

    /** @throws ORMException|AppException */
    public function invite(User $user, User $suer): Invite
    {
        if ($found = $this->inviteRepository->findExistingBetween($user, $suer)) {

            if ( $found->getStatus() === InviteStatus::BLOCKED ) {
                throw new AppException("Blocked connection", 409);
            }

            throw new AppException("Already invited or Invalid target", 406);
        }

        $invite = new Invite($user, $suer);
        $invite->setStatus(InviteStatus::SENT);
        $invite->setValidUntil(new DateTimeImmutable());
        $invite->setHash($this->generateInviteHash());

        $this->entityManager->persist($invite);
        $this->entityManager->flush();

        return $invite;
    }

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

    public function acceptInvite(User $user, Invite $token): Invite
    {
        return $token;
    }

    public function cancelInvite(User $user, Invite $token): Invite
    {
        return $token;
    }

    public function denyInvite(User $user, Invite $token): Invite
    {
        return $token;
    }

    /** @throws RuntimeException */
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
