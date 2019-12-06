<?php

namespace Birdperson;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RayDonovanHandleOurErrorsInThisClass
{
    final public function onKernelException(ExceptionEvent $event): void
    {
        $response = new JsonResponse([]);

        $e = $event->getThrowable();

        if ($e instanceof NotFoundHttpException) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $event->setResponse($response);
    }

}