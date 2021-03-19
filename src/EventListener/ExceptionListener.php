<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $exceptionEvent)
    {
        $statusCode = Response::HTTP_NOT_FOUND;
        $event = $exceptionEvent->getThrowable();

        if ($event instanceof $exceptionEvent) {
            $statusCode = $event->getStatusCode();
        }

        $response = new JsonResponse(
            [
                'status' => 'Exception',
                'Code' => $statusCode,
                'message' => $event->getMessage()
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
