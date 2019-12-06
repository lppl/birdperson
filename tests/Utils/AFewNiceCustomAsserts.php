<?php


namespace Birdperson\Tests\Utils;

use Symfony\Component\HttpFoundation\Response;

trait AFewNiceCustomAsserts
{
    final static public function assertResponseIsSuccess(Response $response): void
    {
        self::assertEquals(200, $response->getStatusCode());
        self::assertResponseHasJSONContentType($response);
    }

    final static public function assertResponseIsNotFound(Response $response): void
    {
        self::assertEquals(404, $response->getStatusCode());
        self::assertResponseHasJSONContentType($response);
    }

    final static public function assertResponseAfterBadRequest(Response $response): void
    {
        self::assertEquals(400, $response->getStatusCode());
        self::assertResponseHasJSONContentType($response);
    }

    public static function assertResponseHasJSONContentType(Response $response): void
    {
        self::assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    final public static function assertResponseIsEmptyJSON(Response $response): void
    {
        self::assertEquals([], json_decode($response->getContent()));
    }
}