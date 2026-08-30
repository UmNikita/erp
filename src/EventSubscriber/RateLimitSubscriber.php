<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RateLimitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(service: 'limiter.user_api')]
        private RateLimiterFactory $userApiLimiter,
        private Security $security,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api/v1/')) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user) {
            return;
        }

        $limiter = $this->userApiLimiter->create(
            (string) $user->getUserIdentifier()
        );

        if (!$limiter->consume()->isAccepted()) {
            $event->setResponse(new JsonResponse([
                'message' => 'Too many requests'
            ], 429));
        }
    }
}