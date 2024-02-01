<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    protected function authError(): JsonResponse
    {
        return $this->json([
            'message' => 'Log in please',
            'code' => Response::HTTP_UNAUTHORIZED
        ], Response::HTTP_UNAUTHORIZED);
    }
}
