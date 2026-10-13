<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClientBundle\HttpClient;

use Superbrave\VerboseErrorHttpClientBundle\HttpClient\Response\VerboseErrorResponse;
use Symfony\Component\HttpClient\Response\ResponseStream;
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
    public function __construct(protected HttpClientInterface $client)
    {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $response = $this->client->request($method, $url, $options);

        return new VerboseErrorResponse($response);
    }

    /**
     * @param VerboseErrorResponse|iterable<array-key, VerboseErrorResponse> $responses
     */
    public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof VerboseErrorResponse) {
            $responses = [$responses];
        }

        return new ResponseStream(VerboseErrorResponse::stream($this->client, $responses, $timeout));
    }

    /**
     * @param array<string, mixed> $options
     */
    public function withOptions(array $options): static
    {
        $clone = clone $this;
        $clone->client = $this->client->withOptions($options);

        return $clone;
    }
}
