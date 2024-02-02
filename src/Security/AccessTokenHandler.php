<?php

namespace App\Security;
use App\Service\AuthTokenService;
use Override;
use SensitiveParameter;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly AuthTokenService $tokenService
    ) {}

    #[Override]
    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        $token = $this->tokenService->validateToken($accessToken);

        if (!$token) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge($token->getUser()->getUserIdentifier());
    }
}
