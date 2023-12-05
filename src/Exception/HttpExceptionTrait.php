<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClient\Exception;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

/**
 * @author Niels Nijens <nn@superbrave.nl>
 * @author Beau Ottens <bo@ehvg.nl>
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
trait HttpExceptionTrait
{
    public function __construct(private readonly ResponseInterface $response, Throwable $previousException)
    {
        $code = $response->getInfo('http_code');
        $url = $response->getInfo('url');
        $message = sprintf('HTTP %d returned for "%s".', $code, $url);

        $httpCodeFound = false;
        $isJson = false;
        foreach (array_reverse($response->getInfo('response_headers')) as $responseHeader) {
            if (str_starts_with($responseHeader, 'HTTP/')) {
                if ($httpCodeFound) {
                    break;
                }

                $message = sprintf('%s returned for "%s".', $responseHeader, $url);
                $httpCodeFound = true;
            }

            if (stripos($responseHeader, 'content-type:') === 0) {
                if (preg_match('/\bjson\b/i', $responseHeader)) {
                    $isJson = true;
                }

                if ($httpCodeFound) {
                    break;
                }
            }
        }

        $contentSummary = $this->getResponseContentSummary($isJson);
        if (empty($contentSummary) === false) {
            $message = sprintf('%s with response: %s', rtrim($message, '.'), $contentSummary);
        }

        parent::__construct($message, $code, $previousException);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Returns a summary of the response content.
     */
    private function getResponseContentSummary(bool $json): string
    {
        $content = $this->response->getContent(false);
        if ($json) {
            return $content;
        }

        $contentSummary = substr($content, 0, 120);
        if (strlen($content) > 120) {
            $contentSummary .= ' (truncated...)';
        }

        return $contentSummary;
    }
}
