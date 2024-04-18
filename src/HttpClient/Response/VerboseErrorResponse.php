<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClientBundle\HttpClient\Response;

use Generator;
use Superbrave\VerboseErrorHttpClientBundle\HttpClient\Exception\ClientException;
use Superbrave\VerboseErrorHttpClientBundle\HttpClient\Exception\RedirectionException;
use Superbrave\VerboseErrorHttpClientBundle\HttpClient\Exception\ServerException;
use Symfony\Contracts\HttpClient\ChunkInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Wraps a response to be able to decorate the thrown exceptions.
 *
 * @author Niels Nijens <nn@superbrave.nl>
 */
readonly class VerboseErrorResponse implements ResponseInterface
{
    public function __construct(private ResponseInterface $response)
    {
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getInfo(?string $type = null): mixed
    {
        return $this->response->getInfo($type);
    }

    public function getHeaders(bool $throw = true): array
    {
        try {
            return $this->response->getHeaders($throw);
        } catch (ServerExceptionInterface $exception) {
            throw new ServerException($this, $exception);
        } catch (ClientExceptionInterface $exception) {
            throw new ClientException($this, $exception);
        } catch (RedirectionExceptionInterface $exception) {
            throw new RedirectionException($this, $exception);
        }
    }

    public function getContent(bool $throw = true): string
    {
        try {
            return $this->response->getContent($throw);
        } catch (ServerExceptionInterface $exception) {
            throw new ServerException($this, $exception);
        } catch (ClientExceptionInterface $exception) {
            throw new ClientException($this, $exception);
        } catch (RedirectionExceptionInterface $exception) {
            throw new RedirectionException($this, $exception);
        }
    }

    public function toArray(bool $throw = true): array
    {
        try {
            return $this->response->toArray($throw);
        } catch (ServerExceptionInterface $exception) {
            throw new ServerException($this, $exception);
        } catch (ClientExceptionInterface $exception) {
            throw new ClientException($this, $exception);
        } catch (RedirectionExceptionInterface $exception) {
            throw new RedirectionException($this, $exception);
        }
    }

    public function cancel(): void
    {
        $this->response->cancel();
    }

    /**
     * @internal
     *
     * @param iterable<VerboseErrorResponse> $responses
     *
     * @return Generator<VerboseErrorResponse, ChunkInterface>
     */
    public static function stream(HttpClientInterface $client, iterable $responses, ?float $timeout): Generator
    {
        foreach ($responses as $response) {
            yield $response => $client->stream($response->response, $timeout);
        }
    }
}
