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

    final public function __invoke(?string $encoded, ?string $currentTime, Clock $clock): Response
    {
        $tokenParts = $this->tokenizer->read($encoded);
        $id = $tokenParts['id'];
        $server = $tokenParts['server'];
        $response = new Response($encoded);
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', "file-$id-$server.txt");

        return $response;
    }
}