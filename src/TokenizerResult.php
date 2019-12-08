<?php

namespace Birdperson;

class TokenizerResult
{
    const INCORRECT_INPUT = 'incorrect_input';

    private bool $hasError;
    private string $error;
    private Token $token;

    private function __construct()
    {
    }

    final public static function withError(string $error): self
    {
        $result = new self();
        $result->hasError = true;
        $result->error = $error;
        $result->token = new Token();
        return $result;
    }

    final public static function withToken(Token $token): self
    {
        $result = new self();
        $result->hasError = false;
        $result->error = '';
        $result->token = $token;
        return $result;
    }

    final public function hasError(): bool
    {
        return $this->hasError;
    }

    final public function error(): bool
    {
        return $this->error;
    }

    final public function token(): Token
    {
        return $this->token;
    }
}