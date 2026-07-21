<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager
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

        $token = $this->jwtManager->create($user);

        $response = $event->getResponse();
        if ($response === null) {
            return;
        }

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
    }
}