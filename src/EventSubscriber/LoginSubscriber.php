<?php

namespace App\EventSubscriber;

use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private RefreshTokenGeneratorInterface $refreshTokenGenerator,
    private RefreshTokenManagerInterface $refreshTokenManager
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        $req = $event->getRequest();
        if($req->getPathInfo() != "/api/login_check" && $req->getPathInfo() != "/login") {
            return;
        }
        
        $response = $event->getResponse();

        $token = $this->jwtManager->create($user);

        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl(
            $user,
            2592000
        );
        $this->refreshTokenManager->save($refreshToken);

        
        $response->headers->setCookie(
            Cookie::create(
                'BEARER',
                $token
            )
            ->withHttpOnly(true)
            ->withSecure(false) 
            ->withSameSite('lax')
            ->withPath('/')
        );

        $response->headers->setCookie(
            Cookie::create(
                'REFRESH_TOKEN',
                $refreshToken->getRefreshToken()
            )
            ->withHttpOnly(true)
            ->withSecure(false)
            ->withSameSite('lax')
            ->withPath('/')
        );
    }
}