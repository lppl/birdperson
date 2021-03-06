<?php

namespace Birdperson\Infrastructure\Handler;

use Birdperson\ResultForFile;
use Birdperson\Tokenizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SendMeThatFatFile
{
    private Tokenizer $tokenizer;

    public function __construct(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    final public function __invoke(string $encoded, Request $request): Response
    {
        $result = $this->tokenizer->read($encoded, $request->getClientIp());

        return $this->formatResponse($result);
    }

    private function formatResponse(ResultForFile $result): Response
    {
        if ($result->hasError()) {
            switch ($result->error()) {
                case ResultForFile::TOKEN_EXPIRED:
                case ResultForFile::WRONG_IP:
                    return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
                case ResultForFile::INCORRECT_INPUT:
                    return new JsonResponse([], Response::HTTP_BAD_REQUEST);
            }
        }

        $url = $result->url();
        $response = new Response($url);
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', basename($url)));

        return $response;
    }
}