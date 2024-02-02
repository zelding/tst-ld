<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\AppException;
use App\Service\InviteService;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ProfileController extends BaseController
{
    public function __construct(private readonly InviteService $inviteService)
    {}

    #[Route('/me', name: 'app_profile', methods: ['GET'])]
    public function index(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if(!$user) {
            return $this->authError();
        }

        try {
            return $this->json($this->inviteService->getUserInviteDataForProfile($user));
        }
        catch(ORMException $exception) {

        }
    }

    #[Route('/me', name: 'app_profile_invite', methods: ['POST'])]
    public function invite(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if(!$user) {
            return $this->authError();
        }

        if ( !$request->query->get('user_id') ) {
            return $this->json([
                 'message' => 'Target missing',
                 'code'    => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }

        if ( $user->getUsername() === $request->query->get('user_id') ) {
            return $this->json([
                 'message' => 'Target invalid',
                 'code'    => Response::HTTP_NOT_ACCEPTABLE
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        try {
            $invite = $this->inviteService->invite($user, $request->query->get('user_id'));

            return $this->json([
                "hash" => $invite->getHash(),
                "inviter" => $invite->getInviter(),
                "invitee" => $invite->getInvitee(),
                "invited_at" => $invite->getCreatedAt()->format('Y-m-d H:i:s')
            ], Response::HTTP_CREATED);
        }
        catch(ORMException $exception) {
            return $this->json([
                'message' => 'Internal error',
                'code'    => $exception->getCode()
            ], $exception->getCode());
        }
        catch (AppException $exception) {
            return $this->json([
                'message' => 'General error',
                'code'    => $exception->getCode()
            ], $exception->getCode());
        }
    }

    #[Route('/me/answer', name: 'app_profile_invite_answer', methods: ['PUT'])]
    public function answer(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if(!$user) {
            return $this->authError();
        }

        if ( !$request->request->get('hash') ) {
            return $this->json([
                'message' => 'Target missing',
                'code'    => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }

        if ( !$request->request->get('answer') ) {
            return $this->json([
                'message' => 'Answer missing',
                'code'    => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            if ($request->request->get('answer')) {
                $this->inviteService->acceptInvite($user, $request->request->get('hash'));
            }
            else {
                $this->inviteService->rejectInvite($user, $request->request->get('hash'));
            }

            return $this->json([]);
        }
        catch(ORMException $exception) {
            return $this->json([
                                   'message' => 'Internal error',
                                   'code'    => $exception->getCode()
                               ], $exception->getCode());
        }
        catch(AppException $exception) {
            return $this->json([
                'message' => 'General error',
                'code'    => $exception->getCode()
            ], $exception->getCode());
        }
    }

    #[Route('/me/answer', name: 'app_profile_invite_undo', methods: ['DELETE'])]
    public function unInvite(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if(!$user) {
            return $this->authError();
        }

        if ( !$request->request->get('hash') ) {
            return $this->json([
                                   'message' => 'Target missing',
                                   'code'    => Response::HTTP_BAD_REQUEST
                               ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->inviteService->cancelInvite($user, $request->request->get('hash'));

            return $this->json([], Response::HTTP_NO_CONTENT);
        }
        catch(ORMException $exception) {
            return $this->json([
                                   'message' => 'Internal error',
                                   'code'    => $exception->getCode()
                               ], $exception->getCode());
        }
        catch(AppException $exception) {
            return $this->json([
                                   'message' => 'General error',
                                   'code'    => $exception->getCode()
                               ], $exception->getCode());
        }
    }
}
