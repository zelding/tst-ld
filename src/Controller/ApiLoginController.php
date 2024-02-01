<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\TokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'app_api_login', methods: ['POST', 'GET'])]
    public function index(#[CurrentUser] ?User $user, TokenService $tokenService): JsonResponse
    {
        if (!$user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $tokenService->createNewToken($user);

        return $this->json([
            'message' => 'Welcome!',
            'user'    => $user->getUserIdentifier(),
            'token'   => $token
        ]);
    }
}
