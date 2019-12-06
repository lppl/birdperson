<?php

namespace Birdperson\Tests;

use Birdperson\Tests\Utils\AFewNiceCustomAsserts;
use Birdperson\Tests\Utils\WebTestCaseShortcuts;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RetrievingTokenHappyPathTest extends WebTestCase
{
    use AFewNiceCustomAsserts;
    use WebTestCaseShortcuts;

    final public function testThatTokenIsSend(): void
    {
        $response = self::fetch('/generator.php', ['id' => 1234]);
        self::assertResponseIsSuccess($response);
    }
}
