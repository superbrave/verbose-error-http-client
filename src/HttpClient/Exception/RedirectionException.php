<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClientBundle\HttpClient\Exception;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

/**
 * Thrown when a 3xx response is returned.
 *
 * @author Niels Nijens <nn@superbrave.nl>
 */
final class RedirectionException extends RuntimeException implements RedirectionExceptionInterface
{
    use HttpExceptionTrait;
}
