<?php

namespace Birdperson\Tests;

use Birdperson\Clock;
use Birdperson\CyclicServerProvider;
use Birdperson\Crypto;
use Birdperson\Tokenizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

class TokenizerProduceGoodTokensTest extends TestCase
{
    const TOKENURL = 'https://host/app/%s';

    final public function incorrectParameters(): array
    {
        return [
            ["2019-12-08 16:00:10", "2019-12-08 16:01:10", 60],
            ["2019-12-08 16:00:10", "2019-12-08 16:02:11", 121],
        ];
    }

    /**
     * @dataProvider incorrectParameters
     */
    final public function testCreationAndValidityTime(
        string $createdAt,
        string $validTill,
        int $tokenLifetime
    ): void
    {
        $clock = new Clock($createdAt);
        $serverProvider = new CyclicServerProvider($clock, 3);
        $tokenizer = new Tokenizer($clock, $tokenLifetime, $serverProvider, self::TOKENURL, $this->crypto());

        $token = $tokenizer->generate($this->getExampleInput())->token();

        $this->assertEquals($createdAt, $token->createdAt);
        $this->assertEquals($validTill, $token->validTill);
    }

    final public function servers(): array
    {
        return [
            ["2019-12-08 16:01:00", 3, 1],
            ["2019-12-08 16:02:00", 3, 2],
            ["2019-12-08 16:03:00", 3, 0],
            ["2019-12-08 16:14:00", 7, 0],
        ];
    }

    /**
     * @dataProvider servers
     */
    final public function testServerCanBeSelectedCyclically(string $currentTime, int $serverCount, int $expectedServer): void
    {
        $clock = new Clock($currentTime);
        $serverProvider = new CyclicServerProvider($clock, $serverCount);
        $tokenizer = new Tokenizer($clock, 60, $serverProvider, self::TOKENURL, $this->crypto());

        $token = $tokenizer->generate($this->getExampleInput())->token();

        $this->assertEquals($expectedServer, $token->server);
    }

    final public function testTokeninzerCanReadDataFromGeneratedUrl(): void
    {
        $clock = new Clock("2019-12-08 16:01:00");
        $serverProvider = new CyclicServerProvider($clock, 3);
        $tokenizer = new Tokenizer($clock, 60, $serverProvider, self::TOKENURL, $this->crypto());

        $token = $tokenizer->generate($this->getExampleInput())->token();

        $junk = explode('/', $token->url);
        $encodedPart = $junk[count($junk) - 1];

        $result = $tokenizer->read($encodedPart);

        $this->assertEquals($token->server, $result['server'], "has same server");
        $this->assertEquals($token->ip, $result['ip'], "has same ip");
        $this->assertEquals($this->getExampleInput()->getInt('id'), $result['id'], "has same id");
    }

    private function getExampleInput(): ParameterBag
    {
        return new ParameterBag([
            'id' => 1134,
            'ip' => 'not-important-here'
        ]);
    }

    private function crypto(): Crypto
    {
        return new Crypto('my secret');
    }

}
