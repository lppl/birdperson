<?php

namespace Birdperson\Tests\Functional;

use Birdperson\Clock;
use Birdperson\Crypto;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DownloadFileTest extends WebTestCase
{
    use Utils\AFewNiceCustomAsserts;
    use Utils\WebTestCaseShortcuts;


    final public function validTokens(): array
    {
        return [
            ["2019-12-08 16:01:00", "2019-12-08 16:01:00", "192.168.1.1", 3, 1],
            ["2019-12-08 16:01:00", "2019-12-08 16:01:59", "192.168.1.1", 2, 1],
            ["2019-12-08 16:01:00", "2019-12-08 16:02:00", "192.168.1.1", 1, 2],
        ];
    }

    /**
     * @dataProvider validTokens
     */
    final public function testThatValidTokenWillEnableFiledownload(string $currentTime, string $validTill, string $ip, int $id, int $server): void
    {
        $client = static::createClient();
        $client->getContainer()->set(Clock::class, new Clock($currentTime));
        $token = $this->fabricateToken($client, $validTill, $ip, $id, $server);

        $client->request('GET', "/$token", [], [], ['REMOTE_ADDR' => $ip]);
        $response = $client->getResponse();

        self::assertResponseIsSuccessful();
        self::assertResponseHasTextFileContentType($response);
        self::assertEquals("http://server-$server.dummy.server/file-$id-$server.txt", $response->getContent());
        self::assertEquals("file-$id-$server.txt", $response->headers->get('Content-Disposition'));
    }

    final public function expiredTokens(): array
    {
        return [
            ["2019-12-08 16:01:00", "2019-12-08 16:02:01", "192.168.1.1", 3, 1],
            ["2019-12-08 16:01:00", "2019-12-09 16:01:00", "192.168.1.1", 2, 2],
        ];
    }

    /**
     * @dataProvider expiredTokens
     */
    final public function testThatExpiredTokesWillFail(string $currentTime, string $validTill, string $ip, int $id, int $server): void
    {
        $client = static::createClient();
        $client->getContainer()->set(Clock::class, new Clock($currentTime));
        $token = $this->fabricateToken($client, $validTill, $ip, $id, $server);

        $client->request('GET', "/$token");
        $response = $client->getResponse();

        self::assertResponseNotAuthorized($response);
        self::assertResponseHasJSONContentType($response);
        self::assertResponseIsEmptyJSON($response);
    }

    final public function tokensFromBadIp(): array
    {
        return [
            ["2019-12-08 16:01:00", "2019-12-08 16:01:01", "192.168.1.1", "192.168.1.2", 3, 1],
            ["2019-12-08 16:01:00", "2024-12-08 16:01:01", "192.168.1.1", "192.168.1.2", 2, 2],
        ];
    }

    /**
     * @dataProvider tokensFromBadIp
     */
    final public function testThatWrongClientIpWontReceiveFile(string $currentTime, string $validTill, string $goodIp, string $badIp, int $id, int $server): void
    {
        $client = static::createClient();
        $client->getContainer()->set(Clock::class, new Clock($currentTime));
        $token = $this->fabricateToken($client, $validTill, $goodIp, $id, $server);

        $client->request('GET', "/$token", [], [], ['REMOTE_ADDR' => $badIp]);
        $response = $client->getResponse();

        self::assertResponseNotAuthorized($response);
        self::assertResponseHasJSONContentType($response);
        self::assertResponseIsEmptyJSON($response);
    }

    final public function testThatInvalidTokenWillFail(): void
    {
        $response = self::fetch('/invalid-token');

        self::assertResponseAfterBadRequest($response);
        self::assertResponseHasJSONContentType($response);
        self::assertResponseIsEmptyJSON($response);
    }

    private function fabricateToken(KernelBrowser $client, string $validTill, string $ip, int $id, int $server): string
    {
        return (new Crypto($client->getContainer()->getParameter('kernel.secret')))->encode(json_encode([
            'ip' => $ip,
            'id' => $id,
            'server' => $server,
            'validTill' => $validTill,
        ]));
    }
}
