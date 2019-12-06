<?php

namespace Birdperson\Handler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SendMeThatTastyToken
{
    final public function __invoke(Request $request): Response
    {
        $id = $request->query->getInt('id', 0);
        if ($id < 1) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse();
    }
}