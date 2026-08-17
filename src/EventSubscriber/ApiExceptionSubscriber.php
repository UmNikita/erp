<?php

namespace App\EventSubscriber;

use App\CRM\Exception\InvalidRequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        $path = $request->getPathInfo();

        if (!str_starts_with($path, '/api/v1') && !str_starts_with($path, '/public-api/v1'))
            return;

        $exception = $event->getThrowable();

        if ($exception instanceof InvalidRequestException) {
            $event->setResponse(
                new JsonResponse([
                    'errors' => $exception->getErrors()
                ], 422)
            );

            return;
        }

        $event->setResponse(
            new JsonResponse([
                'error' => $exception->getMessage(),
            ], 500)
        );
    }
}