<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClient\Exception;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

/**
 * Thrown when a 5xx response is returned.
 */
final class ServerException extends RuntimeException implements ServerExceptionInterface
{
    use HttpExceptionTrait;
}
