<?php

namespace Birdperson\Infrastructure\Handler;

use Birdperson\Clock;
use Birdperson\Tokenizer;
use Symfony\Component\HttpFoundation\Response;

class SendMeThatFatFile
{
    private Tokenizer $tokenizer;

    public function __construct(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    final public function __invoke(string $encoded, Clock $clock): Response
    {
        $result = $this->tokenizer->read($encoded);

        $url = $result->url();
        $response = new Response($encoded);
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', basename($url));

        return $response;
    }
}