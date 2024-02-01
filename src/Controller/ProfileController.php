<?php

namespace App\Controller;

use App\Entity\User;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ProfileController extends BaseController
{
    #[Route('/me', name: 'app_profile', methods: ['GET'])]
    public function index(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if(!$user) {
            return $this->authError();
        }

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProfileController.php',
        ]);
    }

    #[Route('/me', name: 'app_profile_invite', methods: ['POST'])]
    public function invite(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if(!$user) {
            return $this->authError();
        }

        if ( !$request->query->get('user_id') ) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProfileController.php',
        ]);
    }
}
