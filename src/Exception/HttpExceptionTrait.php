<?php

namespace Superbrave\VerboseErrorHttpClient\Exception;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

/**
 * @author Niels Nijens <nn@superbrave.nl>
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
trait HttpExceptionTrait
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param ResponseInterface $response
     * @param Throwable         $previousException
     */
    public function __construct(ResponseInterface $response, Throwable $previousException)
    {
        $this->response = $response;

        $code = $response->getInfo('http_code');
        $url = $response->getInfo('url');
        $message = sprintf('HTTP %d returned for "%s".', $code, $url);

        $httpCodeFound = false;
        $isJson = false;
        foreach (array_reverse($response->getInfo('response_headers')) as $responseHeader) {
            if (strpos($responseHeader, 'HTTP/') === 0) {
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

    /**
     * {@inheritdoc}
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Returns a summary of the response content.
     *
     * @param bool $json
     *
     * @return string
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
