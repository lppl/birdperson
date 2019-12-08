<?php

namespace Birdperson\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class RetrievingTokenHappyPathTest extends WebTestCase
{
    use Utils\AFewNiceCustomAsserts;
    use Utils\WebTestCaseShortcuts;

    const REAL_IP = "192.168.1.1";
    const TRUSTED_PROXY = "192.168.1.2";
    const UNTRUSTED_PROXY = "192.169.33.3";

    final public function testThatTokenIsSend(): void
    {
        $response = self::fetch('/generator.php', ['id' => 1234]);
        self::assertResponseIsSuccess($response);
    }

    final public function testThatResponseHaveCorrectIp(): void
    {
        $response = self::fetch('/generator.php', ['id' => 1324], ['REMOTE_ADDR' => self::REAL_IP]);

        self::assertResponseContain($response, 'ip', self::REAL_IP);
    }

    final public function testThatAppUsesTrustedProxiesIp(): void
    {
        Request::setTrustedProxies([self::TRUSTED_PROXY], Request::HEADER_X_FORWARDED_FOR);
        $response = self::fetch('/generator.php', ['id' => 1324], [
            'REMOTE_ADDR' => self::TRUSTED_PROXY,
            'HTTP_X_FORWARDED_FOR' => self::REAL_IP
        ]);
        self::assertResponseContain($response, 'ip', self::REAL_IP);
    }

    final public function testThatAppIgnoreUntrustedProxies(): void
    {
        Request::setTrustedProxies([self::TRUSTED_PROXY], Request::HEADER_X_FORWARDED_FOR);
        $response = self::fetch('/generator.php', ['id' => 1324], [
            'REMOTE_ADDR' => self::UNTRUSTED_PROXY,
            'HTTP_X_FORWARDED_FOR' => self::REAL_IP
        ]);
        self::assertResponseContain($response, 'ip', self::UNTRUSTED_PROXY);
    }

    final public function testTokenHaveGenerationAndExpirationTime(): void
    {
        $response = self::fetch('/generator.php', ['id' => 1324]);

        self::assertResponseContainField($response, 'createdAt');
        self::assertResponseContainField($response, 'validTill');
    }

    final protected function setUp(): void
    {
        Request::setTrustedProxies([], Request::HEADER_FORWARDED);
    }
}