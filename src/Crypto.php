<?php

namespace Birdperson;

class Crypto
{
    private string $secret;
    private string $method;

    public function __construct(string $secret, string $method = 'AES-256-CBC')
    {
        $this->secret = $secret;
        $this->method = $method;
    }

    final public function encode(string $decoded): string
    {
        return base64_encode(openssl_encrypt($decoded, $this->method, $this->key(), 0, $this->iv()));
    }

    final public function decode(string $encoded): string
    {
        return openssl_decrypt(base64_decode($encoded), $this->method, $this->key(), 0, $this->iv());
    }

    private function iv(): string
    {
        return substr(hash('sha256', $this->secret), 0, 16);
    }

    private function key(): string
    {
        return hash('sha256', $this->secret);
    }
}