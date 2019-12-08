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
            ["2019-12-08 16:01:00", "2019-12-08 16:02:30", "192.168.1.1", 2, 2],
            ["2019-12-08 16:01:00", "2019-12-08 16:02:59", "192.168.1.1", 1, 3],
        ];
    }

    /**
     * @dataProvider validTokens
     */
    final public function testDownloadFileWithValidToken(string $currentTime, string $validTill, string $ip, int $id, int $server): void
    {
        $client = static::createClient();
        $client->getContainer()->set(Clock::class, new Clock($currentTime));
        $token = $this->fabricateToken($client, $validTill, $ip, $id, $server);

        $client->request('GET', "/$token");
        $response = $client->getResponse();

        self::assertResponseHasTextFileContentType($response);
        self::assertEquals($token, $response->getContent());
        self::assertEquals("file-$id-$server.txt", $response->headers->get('Content-Disposition'));
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
