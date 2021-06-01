<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if($exception instanceof NotFoundHttpException) {
            $data = [
                "status" => $exception->getStatusCode(),
                "message" => 'Resource not found'
            ];

            $event->setResponse(new JsonResponse(
                $data, 
                $exception->getStatusCode(),
                ['Content-Type' => 'application/problem+json']
            ));
        }
        
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
