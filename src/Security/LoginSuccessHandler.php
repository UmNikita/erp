<?php

namespace App\Security;

use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGenerator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginSuccessHandler
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private RefreshTokenGenerator $refreshTokenGenerator
    ) {
    }

    public function __invoke(UserInterface $user): JsonResponse
    {
        $jwt = $this->jwtManager->create($user);

        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl(
            $user,
            2592000
        );

        return new JsonResponse([
            'token' => $jwt,
            'refresh_token' => $refreshToken->getRefreshToken()
        ]);
    }
}