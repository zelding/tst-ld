<?php

namespace App\Service;

use App\Entity\Invite;
use App\Entity\User;
use App\Model\InviteStatus;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class InviteService
{
    public function __construct(private UserRepository $userRepository) {}

    public function invite(User $user, User $suer)
    {
        //$this->tokenService->createNewToken($user);
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

    public function acceptInvite(User $user, Invite $token)
    {

    }

    public function cancelInvite(User $user, Invite $token)
    {

    }

    public function denyInvite(User $user, Invite $token)
    {

    }
}
