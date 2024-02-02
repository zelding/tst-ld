<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthTokenService;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    #[Route('/login', name: 'api_login', methods: ['POST', 'GET'])]
    public function index(#[CurrentUser] ?User $user, AuthTokenService $tokenService): JsonResponse
    {
        if (!$user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $token = $tokenService->authenticate($user);

            return $this->json([
                'message'  => 'Welcome!',
                'user'     => $user->getUserIdentifier(),
                'token'    => $token->getToken(),
                'validity' => $token->getValidUntil()->format('Y-m-d H:i:s')
            ]);
        }
        catch(ORMException $exception) {
            return $this->json([
                'message' => 'Internal error',
                'code'    => Response::HTTP_INTERNAL_SERVER_ERROR,
                'ex' => $exception->getMessage()
                // trace in debug mode ?
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
