<?php

namespace Birdperson;

use Symfony\Component\HttpFoundation\ParameterBag;

class Tokenizer
{
    private Clock $clock;
    private int $tokenLifetime;
    private ServerProvider $serverProvider;
    private Server $server;
    private string $tokenUrl;
    private Crypto $crypto;

    public function __construct(
        Clock $clock,
        int $tokenLifetime,
        ServerProvider $serverProvider,
        string $tokenUrl,
        Crypto $crypto
    )
    {
        $this->clock = $clock;
        $this->tokenLifetime = $tokenLifetime;
        $this->serverProvider = $serverProvider;
        $this->server = $this->serverProvider->getBestPossibleServer();
        $this->tokenUrl = $tokenUrl;
        $this->crypto = $crypto;
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
        $token->server = $this->server->id();

        $token->url = $this->urlFor([
            'ip' => $input->get('ip'),
            'id' => $input->getInt('id'),
            'server' => $token->server,
            'validTill' => $token->validTill,
        ]);

        return TokenizerResult::withToken($token);
    }

    final public function read(string $encoded)
    {
        return json_decode($this->crypto->decode($encoded), true);
    }

    private function urlFor(array $data)
    {
        $encoded  = $this->crypto->encode(json_encode($data));
        return sprintf($this->tokenUrl, $encoded);
    }
}