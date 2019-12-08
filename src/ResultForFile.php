<?php

namespace Birdperson;

class ResultForFile
{
    const INCORRECT_INPUT = 'incorrect_input';
    const TOKEN_EXPIRED = 'token_expired';

    private bool $hasError;
    private string $error;
    private string $url;

    private function __construct()
    {
    }

    final public static function withError(string $error): self
    {
        $result = new self();
        $result->hasError = true;
        $result->error = $error;
        $result->url = '';
        return $result;
    }

    final public static function withFile(string $url): self
    {
        $result = new self();
        $result->hasError = false;
        $result->error = '';
        $result->url = $url;
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

    final public function url(): string
    {
        return $this->url;
    }

}