<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class SendMeThatTastyToken
{
    final public function sendToken(): Response
    {
        return new Response(Response::HTTP_OK);
    }
}