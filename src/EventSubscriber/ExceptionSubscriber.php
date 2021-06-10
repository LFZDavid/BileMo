<?php

namespace App\EventSubscriber;

use Throwable;
use App\ApiProblem;
use App\Exception\ApiProblemException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        /** @var Throwable $exception */
        $e = $event->getThrowable();

        if($e instanceof ApiProblemException) {
            $apiProblem = $e->getApiProblem();
            
        }else {
            $statusCode = 500;
            
            if($e instanceof HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();
            }
            
            $apiProblem = new ApiProblem($statusCode);

            if($statusCode !== 404){
                $apiProblem->set('detail', $e->getMessage());
            }
        }

        $response = new JsonResponse(
            $apiProblem->toArray(),
            $apiProblem->getStatusCode()
        );
        $response->headers->set('Content-Type', 'application/problem+json');
        $event->setResponse($response);
        
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
