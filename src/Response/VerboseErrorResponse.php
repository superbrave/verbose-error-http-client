<?php

namespace Superbrave\VerboseErrorHttpClient\Response;

use Superbrave\VerboseErrorHttpClient\Exception\ClientException;
use Superbrave\VerboseErrorHttpClient\Exception\RedirectionException;
use Superbrave\VerboseErrorHttpClient\Exception\ServerException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
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

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(string $type = null): mixed
    {
        return $this->response->getInfo($type);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function cancel(): void
    {
        $this->response->cancel();
    }
}
