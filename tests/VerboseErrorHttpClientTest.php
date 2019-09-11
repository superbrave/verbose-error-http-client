<?php

namespace Superbrave\VerboseErrorHttpClient\Tests;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Superbrave\VerboseErrorHttpClient\Exception\ClientException;
use Superbrave\VerboseErrorHttpClient\Exception\RedirectionException;
use Superbrave\VerboseErrorHttpClient\Exception\ServerException;
use Superbrave\VerboseErrorHttpClient\VerboseErrorHttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Response\ResponseStream;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Tests the @see VerboseErrorHttpClient class.
 *
 * @author Niels Nijens <nn@superbrave.nl>
 */
class VerboseErrorHttpClientTest extends TestCase
{
    /**
     * @var VerboseErrorHttpClient
     */
    private $httpClient;

    /**
     * @var MockHttpClient
     */
    private $mockHttpClient;

    /**
     * @var MockResponse[]
     */
    private $mockResponses;

    /**
     * Creates a new VerboseErrorHttpClient instance for testing.
     */
    protected function setUp(): void
    {
        $this->mockResponses = new ArrayIterator();
        $this->mockHttpClient = new MockHttpClient($this->mockResponses);

        $this->httpClient = new VerboseErrorHttpClient($this->mockHttpClient);
    }

    /**
     * @dataProvider provideServerExceptionResponses
     *
     * @param MockResponse $response
     * @param string       $expectedExceptionMessage
     */
    public function testRequestThrowsServerException(MockResponse $response, string $expectedExceptionMessage): void
    {
        $this->mockResponses[] = $response;

        $this->expectException(ServerException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $request = $this->httpClient->request('GET', 'https://superbrave.nl');
        $request->getHeaders();
    }

    /**
     * @dataProvider provideClientExceptionResponses
     *
     * @param MockResponse $response
     * @param string       $expectedExceptionMessage
     */
    public function testRequestThrowsClientException(MockResponse $response, string $expectedExceptionMessage): void
    {
        $this->mockResponses[] = $response;

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $request = $this->httpClient->request('GET', 'https://superbrave.nl');
        $request->getHeaders();
    }

    /**
     * @dataProvider provideRedirectionExceptionResponses
     *
     * @param MockResponse $response
     * @param string       $expectedExceptionMessage
     */
    public function testRequestThrowsRedirectionException(
        MockResponse $response,
        string $expectedExceptionMessage
    ): void {
        $this->mockResponses[] = $response;

        $this->expectException(RedirectionException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $request = $this->httpClient->request('GET', 'https://superbrave.nl');
        $request->getHeaders();
    }

    /**
     * Tests if VerboseErrorHttpClient::stream only calls the stream method on
     * the underlying/decorated HTTP client.
     */
    public function testStream(): void
    {
        $mockResponse = new MockResponse('');
        $expectedResponseStream = new ResponseStream(MockResponse::stream(array($mockResponse), null));

        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->getMock();
        $httpClientMock->expects($this->once())
            ->method('stream')
            ->with($mockResponse, null)
            ->willReturn($expectedResponseStream);

        $httpClient = new VerboseErrorHttpClient($httpClientMock);

        $responseStream = $httpClient->stream($mockResponse);

        $this->assertSame($expectedResponseStream, $responseStream);
    }

    /**
     * @return array
     */
    public function provideServerExceptionResponses(): array
    {
        return array(
            array(
                new MockResponse(
                    '',
                    array(
                        'http_code' => 500,
                    )
                ),
                'HTTP 500 returned for "https://superbrave.nl/".',
            ),
            array(
                new MockResponse(
                    '<html><head><title>Internal Server Error</title></head><body><h1>Internal Server Error</h1></body></body></html>',
                    array(
                        'http_code' => 500,
                    )
                ),
                'HTTP 500 returned for "https://superbrave.nl/" with response: <html><head><title>Internal Server Error</title></head><body><h1>Internal Server Error</h1></body></body></html>',
            ),
            array(
                new MockResponse(
                    '<html><head><title>Internal Server Error</title></head><body><h1>Internal Server Error</h1><p>This is an unexpected error. Sorry!</p></body></body></html>',
                    array(
                        'http_code' => 500,
                    )
                ),
                'HTTP 500 returned for "https://superbrave.nl/" with response: <html><head><title>Internal Server Error</title></head><body><h1>Internal Server Error</h1><p>This is an unexpected erro (truncated...)',
            ),
            array(
                new MockResponse(
                    '{"message": "Internal Server Error."}',
                    array(
                        'http_code' => 500,
                        'response_headers' => array(
                            'content-type' => 'application/json',
                        ),
                    )
                ),
                'HTTP 500 returned for "https://superbrave.nl/" with response: {"message": "Internal Server Error."}',
            ),
        );
    }

    /**
     * @return array
     */
    public function provideClientExceptionResponses(): array
    {
        return array(
            array(
                new MockResponse(
                    '',
                    array(
                        'http_code' => 400,
                    )
                ),
                'HTTP 400 returned for "https://superbrave.nl/".',
            ),
            array(
                new MockResponse(
                    '<html><head><title>Bad Request</title></head><body><h1>Bad Request</h1></body></body></html>',
                    array(
                        'http_code' => 400,
                    )
                ),
                'HTTP 400 returned for "https://superbrave.nl/" with response: <html><head><title>Bad Request</title></head><body><h1>Bad Request</h1></body></body></html>',
            ),
            array(
                new MockResponse(
                    '<html><head><title>Bad Request</title></head><body><h1>Bad Request</h1><p>Bad client! Bad! No naughty requests allowed.</p></body></body></html>',
                    array(
                        'http_code' => 400,
                    )
                ),
                'HTTP 400 returned for "https://superbrave.nl/" with response: <html><head><title>Bad Request</title></head><body><h1>Bad Request</h1><p>Bad client! Bad! No naughty requests allowed.< (truncated...)',
            ),
            array(
                new MockResponse(
                    '{"message": "Bad Request."}',
                    array(
                        'http_code' => 400,
                        'response_headers' => array(
                            'content-type' => 'application/json',
                        ),
                    )
                ),
                'HTTP 400 returned for "https://superbrave.nl/" with response: {"message": "Bad Request."}',
            ),
        );
    }

    /**
     * @return array
     */
    public function provideRedirectionExceptionResponses(): array
    {
        return array(
            array(
                new MockResponse(
                    '',
                    array(
                        'http_code' => 301,
                    )
                ),
                'HTTP 301 returned for "https://superbrave.nl/".',
            ),
            array(
                new MockResponse(
                    '<html><head><title>Redirected to: https://superbrave.nl/</title></head><body></body></body></html>',
                    array(
                        'http_code' => 301,
                    )
                ),
                'HTTP 301 returned for "https://superbrave.nl/" with response: <html><head><title>Redirected to: https://superbrave.nl/</title></head><body></body></body></html>',
            ),
            array(
                new MockResponse(
                    '<html><head><title>Redirected to: https://superbrave.nl/</title></head><body><h1>Redirected to: https://superbrave.nl/</h1><p>We should have redirected you to another URL, but we did not...</p></body></body></html>',
                    array(
                        'http_code' => 301,
                    )
                ),
                'HTTP 301 returned for "https://superbrave.nl/" with response: <html><head><title>Redirected to: https://superbrave.nl/</title></head><body><h1>Redirected to: https://superbrave.nl/</ (truncated...)',
            ),
        );
    }
}
