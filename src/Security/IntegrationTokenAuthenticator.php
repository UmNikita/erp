<?php

namespace App\Security;

use App\Repository\IntegrationTokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class IntegrationTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private IntegrationTokenRepository $repository
    ) {}

    public function supports(Request $request): ?bool
    {
        return str_starts_with(
            $request->getPathInfo(),
            '/public-api'
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response {
        return new JsonResponse([
            'error' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }


    public function authenticate(Request $request): Passport
    {
        $token = $this->getToken($request);

        if (!$token) {
            throw new AuthenticationException('Token missing');
        }

        $integrationToken = $this->repository->findActiveToken($token);

        if (!$integrationToken) {
            throw new AuthenticationException('Invalid token');
        }


        return new SelfValidatingPassport(
            new UserBadge(
                'integration_'.$integrationToken->getId(),
                function () use ($integrationToken) {
                    return new IntegrationUser($integrationToken);
                }
            )
        );
    }

    private function getToken(Request $request): ?string
    {
        $header = $request->headers->get('Authorization');

        if (!$header) {
            return null;
        }

        if (!str_starts_with($header, 'Bearer ')) {
            return null;
        }

        return substr($header, 7);
    }
}