<?php

namespace Birdperson;

use Symfony\Component\HttpFoundation\ParameterBag;

class Tokenizer
{
    private Clock $clock;
    private int $tokenLifetime;

    public function __construct(Clock $clock, int $tokenLifetime)
    {
        $this->clock = $clock;
        $this->tokenLifetime = $tokenLifetime;
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

        return TokenizerResult::withToken($token);
    }
}