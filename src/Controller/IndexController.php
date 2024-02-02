<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class IndexController extends AbstractController
{
    #[Route(path: "/")]
    public function index(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        return $this->json([
            "request" => $request->query->all(),
            "login"   => null !== $user,
            "id"      => $user?->getUserIdentifier()
        ]);
    }
}
