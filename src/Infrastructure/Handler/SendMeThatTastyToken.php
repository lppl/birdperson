<?php

namespace Birdperson\Infrastructure\Handler;

use Birdperson\Tokenizer;
use Birdperson\TokenizerResult;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $inputData = [
            'ip' => $request->getClientIp(),
            'id' => $request->query->getInt('id', 0)
        ];

        $result = $this->tokenizer->generate($inputData);

        return $this->formatResult($result);
    }

    private function formatResult(TokenizerResult $result): JsonResponse
    {
        if ($result->hasError()) {
            switch ($result->error()) {
                case TokenizerResult::INCORRECT_INPUT:
                    return new JsonResponse([], Response::HTTP_BAD_REQUEST);
            }
        }
        return new JsonResponse($result->token());
    }
}