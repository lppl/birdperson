<?php

namespace Birdperson\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RetrievingTokenWillFailWithImproperRequestTest extends WebTestCase
{
    use Utils\AFewNiceCustomAsserts;
    use Utils\WebTestCaseShortcuts;

    final public function incorrectParameters(): array
    {
        return [
            [[], "when no parameters are give"],
            [['id' => ''], "when empty id is given"],
            [['id' => 'undefined'], "when some poor shmuck misspelled something in javascript"],
        ];
    }

    /**
     * @dataProvider incorrectParameters
     */
    final public function testThatThereWillBeNoTokenForNotExistentFile(array $parameters): void
    {
        $response = self::fetch('/generator.php', $parameters);
        self::assertResponseAfterBadRequest($response);
        self::assertResponseHasJSONContentType($response);
        self::assertResponseIsEmptyJSON($response);
    }
}
