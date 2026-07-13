<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\User\UserCreatedEvent;
use App\Shared\Services\EmailService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class UserLifecycleEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EmailService $emailService,
        #[Autowire('%app.frontend_url%')]
        private readonly string $frontendUrl,
        #[Autowire('%kernel.environment%')]
        private readonly string $environment
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::class => 'sendWelcomeEmail',
        ];
    }

    public function sendWelcomeEmail(UserCreatedEvent $event): void
    {
        if ($this->environment !== 'prod') {
            return;
        }
        $email = $event->getEmail();
        $context = [
            "user_name" => $event->getName(),
            "department_name" => $event->getNameDepartment(),
            "role_name" => $event->getNameRole(),
            "userEmail" => $email,
            "password" => $event->getPassword(),
            "link" => $this->frontendUrl
        ];
        $this->emailService->sendTemplate("Добро пожаловать в систему!", $email, "emails/user_created.html.twig", $context);
    }
}