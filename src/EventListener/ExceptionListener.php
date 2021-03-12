<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $exceptionEvent)
    {
        $statusCode = '500';

        $event = $exceptionEvent->getThrowable();
        if (is_subclass_of($event, 'Symfony\Component\HttpKernel\Event\ExceptionEvent')) {
            $statusCode = $event->getStatusCode();
        }

        $response = new JsonResponse(
            [
                'status' => 'Exception',
                'Code' => $statusCode,
                'message' => $event->getMessage(),
            ],
            $statusCode
        );

        $response->headers->set('Content-Type', 'application/problem+json');
        $exceptionEvent->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException'
        );
    }
}
