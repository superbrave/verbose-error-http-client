<?php

namespace Superbrave\VerboseErrorHttpClient\Exception;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

/**
 * Thrown when a 4xx response is returned.
 *
 * @author Niels Nijens <nn@superbrave.nl>
 */
final class ClientException extends RuntimeException implements ClientExceptionInterface
{
    use HttpExceptionTrait;
}
