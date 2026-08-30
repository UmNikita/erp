<?php

namespace App\EventSubscriber;

use App\Entity\RefreshToken;
use Doctrine\ORM\EntityManagerInterface;
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
        private RefreshTokenManagerInterface $refreshTokenManager,
        private EntityManagerInterface $entityManager
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

        $this->entityManager
        ->createQueryBuilder()
        ->delete(RefreshToken::class, 'rt')
        ->where('rt.username = :username')
        ->setParameter('username', $user->getUserIdentifier())
        ->getQuery()
        ->execute();
        
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
                'refresh_token',
                $refreshToken->getRefreshToken()
            )
            ->withHttpOnly(true)
            ->withSecure(false)
            ->withSameSite('lax')
            ->withPath('/')
        );
    }
}