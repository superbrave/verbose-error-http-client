<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClient\Exception;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

/**
 * Thrown when a 4xx response is returned.
 */
final class ClientException extends RuntimeException implements ClientExceptionInterface
{
    use HttpExceptionTrait;
}
