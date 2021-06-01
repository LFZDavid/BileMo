<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        /** @var Throwable $exception */
        $exception = $event->getThrowable();
        
        if($exception instanceof NotFoundHttpException) {
            $message = 'Ressource introuvable';
        }

        if($exception instanceof AccessDeniedHttpException) {
            $message = $exception->getMessage();
        }

        $event->setResponse(new JsonResponse(
            [
                "status" => $exception->getStatusCode(),
                "message" => $message,
            ], 
            $exception->getStatusCode(),
            ['Content-Type' => 'application/problem+json']
        ));
        
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
