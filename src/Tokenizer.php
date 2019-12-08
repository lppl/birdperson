<?php

namespace Birdperson;

class Tokenizer
{
    final public function generate(array $data): TokenizerResult
    {
        if ($data['id'] < 1) {
            return TokenizerResult::withError(TokenizerResult::INCORRECT_INPUT);
        }

        $token = new Token();
        $token->ip = $data['ip'];

        return TokenizerResult::withToken($token);
    }
}