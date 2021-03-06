<?php


namespace Birdperson\Tests\Functional\Utils;

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

    final static public function assertResponseNotAuthorized(Response $response): void
    {
        self::assertEquals(401, $response->getStatusCode());
        self::assertResponseHasJSONContentType($response);
    }

    public static function assertResponseHasJSONContentType(Response $response): void
    {
        self::assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public static function assertResponseHasTextFileContentType(Response $response): void
    {
        self::assertEquals('text/plain; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    final public static function assertResponseIsEmptyJSON(Response $response): void
    {
        self::assertEquals([], json_decode($response->getContent()));
    }

    final public function assertResponseContain(Response $response, string $field, $expectedValue): void
    {
        self::assertResponseContainField($response, $field);
        $data = json_decode($response->getContent(), true);
        self::assertEquals(
            $expectedValue,
            $data[$field],
            sprintf('Response contain incorrect value for field "%s"', $field)
        );
    }

    final public function assertResponseContainValidUrlAtField(Response $response, string $field): void
    {
        self::assertResponseContainField($response, $field);
        $data = json_decode($response->getContent(), true);
        self::assertTrue(
            false !== filter_var($data[$field], FILTER_VALIDATE_URL),
            sprintf('Response contain does not contain well formatted url at field "%s"', $field)
        );
    }

    final public function assertResponseContainField(Response $response, string $field): void
    {
        $data = json_decode($response->getContent(), true);
        self::assertArrayHasKey($field, $data, sprintf('Response do not contain field "%s"', $field));
    }
}