<?php

namespace Birdperson\Infrastructure\Handler;

use Birdperson\Tokenizer;
use Birdperson\ResultForToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SendMeThatTastyToken
{
    private Tokenizer $tokenizer;

    public function __construct(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    final public function __invoke(Request $request): Response
    {
        $data = new ParameterBag([
            'ip' => $request->getClientIp(),
            'id' => $request->get('id')
        ]);

        $result = $this->tokenizer->generate($data);

        return $this->formatResult($result);
    }

    private function formatResult(ResultForToken $result): JsonResponse
    {
        if ($result->hasError()) {
            switch ($result->error()) {
                case ResultForToken::INCORRECT_INPUT:
                    return new JsonResponse([], Response::HTTP_BAD_REQUEST);
            }
        }
        return new JsonResponse($result->token());
    }
}