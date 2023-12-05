<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClient\Exception;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

/**
 * Thrown when a 4xx response is returned.
 *
 * @author Niels Nijens <nn@superbrave.nl>
 */
final class ClientException extends \RuntimeException implements ClientExceptionInterface
{
    use HttpExceptionTrait;
}
