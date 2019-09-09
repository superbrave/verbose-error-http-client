<?php

namespace Superbrave\VerboseErrorHttpClient;

use Superbrave\VerboseErrorHttpClient\Response\VerboseErrorResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

/**
 * Decorates a Symfony HTTP client implementation with verbose exception messages.
 *
 * @author Niels Nijens <nn@superbrave.nl>
 */
class VerboseErrorHttpClient implements HttpClientInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * Constructs a new VerboseErrorHttpClient instance.
     *
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $response = $this->httpClient->request($method, $url, $options);

        return new VerboseErrorResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->httpClient->stream($responses, $timeout);
    }
}
