<?php

namespace Superbrave\VerboseErrorHttpClient\Exception;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

/**
 * Thrown when a 5xx response is returned.
 *
 * @author Niels Nijens <nn@superbrave.nl>
 */
final class ServerException extends RuntimeException implements ServerExceptionInterface
{
    use HttpExceptionTrait;
}
