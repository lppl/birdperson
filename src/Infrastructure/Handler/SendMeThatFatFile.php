<?php

namespace Birdperson\Infrastructure\Handler;

use Birdperson\Clock;
use Birdperson\ResultForFile;
use Birdperson\Tokenizer;
use Symfony\Component\HttpFoundation\JsonResponse;
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


        return $this->formatResponse($encoded, $result);
    }

    private function formatResponse(string $encoded, ResultForFile $result): Response
    {
        if ($result->hasError()) {
            switch ($result->error()) {
                case ResultForFile::TOKEN_EXPIRED:
                    return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
                case ResultForFile::INCORRECT_INPUT:
                    return new JsonResponse([], Response::HTTP_BAD_REQUEST);
            }
        }

        $url = $result->url();
        $response = new Response($encoded);
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', basename($url));

        return $response;
    }
}