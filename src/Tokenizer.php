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

    final public function generate(ParameterBag $input): ResultForToken
    {
        if ($input->getInt('id', 0) < 1) {
            return ResultForToken::withError(ResultForToken::INCORRECT_INPUT);
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

        return ResultForToken::withToken($token);
    }

    final public function read(string $encoded, string $userIp): ResultForFile
    {
        $data = json_decode($this->crypto->decode($encoded), true);

        if (null === $data) {
            return ResultForFile::withError(ResultForFile::INCORRECT_INPUT);
        }

        ['id' => $id, 'ip' => $ip, 'server' => $server, 'validTill' => $validTill] = $data;

        if ($ip !== $userIp) {
            return ResultForFile::withError(ResultForFile::WRONG_IP);
        }

        if ($this->clock->timeAfter($this->tokenLifetime) < $validTill) {
            return ResultForFile::withError(ResultForFile::TOKEN_EXPIRED);
        }

        return ResultForFile::withFile($this->serverProvider->getServer($server)->getUrlForFile($id));
    }

    private function urlFor(array $data): string
    {
        $encoded = $this->crypto->encode(json_encode($data));
        return sprintf($this->tokenUrl, $encoded);
    }
}