<?php


namespace Birdperson\Tests\Utils;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

trait WebTestCaseShortcuts
{
    final public static function fetch(string $uri = '', array $parameters = []): Response
    {
        $client = static::createClient();
        assert($client instanceof KernelBrowser);

        $client->request('GET', $uri, $parameters);

        return $client->getResponse();
    }
}