<?php

namespace Birdperson;

use Symfony\Component\HttpFoundation\ParameterBag;

class Tokenizer
{
    private Clock $clock;
    private int $tokenLifetime;
    private ServerProvider $serverProvider;

    public function __construct(Clock $clock, int $tokenLifetime, ServerProvider $serverProvider)
    {
        $this->clock = $clock;
        $this->tokenLifetime = $tokenLifetime;
        $this->serverProvider = $serverProvider;
    }

    final public function generate(ParameterBag $input): TokenizerResult
    {
        if ($input->getInt('id', 0) < 1) {
            return TokenizerResult::withError(TokenizerResult::INCORRECT_INPUT);
        }

        $token = new Token();
        $token->ip = $input->get('ip');
        $token->createdAt = $this->clock->currentTime();
        $token->validTill = $this->clock->timeAfter($this->tokenLifetime);
        $token->server = $this->serverProvider->getBestPossibleServer()->id();

        return TokenizerResult::withToken($token);
    }
}