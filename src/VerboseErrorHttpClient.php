<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClient;

use Superbrave\VerboseErrorHttpClient\Response\VerboseErrorResponse;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

/**
 * Decorates a Symfony HTTP client implementation with verbose exception messages.
 */
class VerboseErrorHttpClient implements HttpClientInterface
{
    use HttpClientTrait;

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $response = $this->httpClient->request($method, $url, $options);

        return new VerboseErrorResponse($response);
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->httpClient->stream($responses, $timeout);
    }
}
