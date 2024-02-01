<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class IndexController
{
    #[Route(path: "/")]
    public function index(Request $request): JsonResponse
    {
        return new JsonResponse(["asd" => "fsd"]);
    }
}
