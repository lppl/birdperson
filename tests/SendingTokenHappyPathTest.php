<?php

namespace Birdperson\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SendingTokenHappyPathTest extends WebTestCase
{
    final public function testThatTokenIsSend(): void
    {
        $client = static::createClient();

        $client->request('GET', '/generator.php');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
