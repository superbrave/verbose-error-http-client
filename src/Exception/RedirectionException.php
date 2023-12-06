<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClient\Exception;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

/**
 * Thrown when a 3xx response is returned.
 */
final class RedirectionException extends RuntimeException implements RedirectionExceptionInterface
{
    use HttpExceptionTrait;
}
