<?php

namespace Birdperson\Tests;

use Birdperson\Clock;
use Birdperson\Tokenizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

class TokenizerProduceGoodTokens extends TestCase
{
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
        $tokenizer = new Tokenizer($clock, $tokenLifetime);

        $token = $tokenizer->generate($this->getExampleInput())->token();

        $this->assertEquals($createdAt, $token->createdAt);
        $this->assertEquals($validTill, $token->validTill);
    }

    private function getExampleInput(): ParameterBag
    {
        return new ParameterBag([
            'id' => 1134,
            'ip' => 'not-important-here'
        ]);
    }

}
