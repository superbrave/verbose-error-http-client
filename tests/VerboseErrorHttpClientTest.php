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
final class VerboseErrorHttpClientTest extends TestCase
{
    private VerboseErrorHttpClient $httpClient;

    private ArrayIterator $mockResponses;

    /**
     * Creates a new VerboseErrorHttpClient instance for testing.
     */
    protected function setUp(): void
    {
        $this->mockResponses = new ArrayIterator();
        $mockHttpClient = new MockHttpClient($this->mockResponses);

        $this->httpClient = new VerboseErrorHttpClient($mockHttpClient);
    }

    /**
     * @dataProvider provideServerExceptionResponses
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
        $expectedResponseStream = new ResponseStream(MockResponse::stream([$mockResponse], null));

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

    public function provideServerExceptionResponses(): array
    {
        return [
            [
                new MockResponse(
                    '',
                    [
                        'http_code' => 500,
                    ]
                ),
                'HTTP 500 returned for "https://superbrave.nl/".',
            ],
            [
                new MockResponse(
                    '<html><head><title>Internal Server Error</title></head><body><h1>Internal Server Error</h1></body></body></html>',
                    [
                        'http_code' => 500,
                    ]
                ),
                'HTTP 500 returned for "https://superbrave.nl/" with response: <html><head><title>Internal Server Error</title></head><body><h1>Internal Server Error</h1></body></body></html>',
            ],
            [
                new MockResponse(
                    '<html><head><title>Internal Server Error</title></head><body><h1>Internal Server Error</h1><p>This is an unexpected error. Sorry!</p></body></body></html>',
                    [
                        'http_code' => 500,
                    ]
                ),
                'HTTP 500 returned for "https://superbrave.nl/" with response: <html><head><title>Internal Server Error</title></head><body><h1>Internal Server Error</h1><p>This is an unexpected erro (truncated...)',
            ],
            [
                new MockResponse(
                    '{"message": "Internal Server Error."}',
                    [
                        'http_code' => 500,
                        'response_headers' => [
                            'content-type' => 'application/json',
                        ],
                    ]
                ),
                'HTTP 500 returned for "https://superbrave.nl/" with response: {"message": "Internal Server Error."}',
            ],
        ];
    }

    public function provideClientExceptionResponses(): array
    {
        return [
            [
                new MockResponse(
                    '',
                    [
                        'http_code' => 400,
                    ]
                ),
                'HTTP 400 returned for "https://superbrave.nl/".',
            ],
            [
                new MockResponse(
                    '<html><head><title>Bad Request</title></head><body><h1>Bad Request</h1></body></body></html>',
                    [
                        'http_code' => 400,
                    ]
                ),
                'HTTP 400 returned for "https://superbrave.nl/" with response: <html><head><title>Bad Request</title></head><body><h1>Bad Request</h1></body></body></html>',
            ],
            [
                new MockResponse(
                    '<html><head><title>Bad Request</title></head><body><h1>Bad Request</h1><p>Bad client! Bad! No naughty requests allowed.</p></body></body></html>',
                    [
                        'http_code' => 400,
                    ]
                ),
                'HTTP 400 returned for "https://superbrave.nl/" with response: <html><head><title>Bad Request</title></head><body><h1>Bad Request</h1><p>Bad client! Bad! No naughty requests allowed.< (truncated...)',
            ],
            [
                new MockResponse(
                    '{"message": "Bad Request."}',
                    [
                        'http_code' => 400,
                        'response_headers' => [
                            'content-type' => 'application/json',
                        ],
                    ]
                ),
                'HTTP 400 returned for "https://superbrave.nl/" with response: {"message": "Bad Request."}',
            ],
        ];
    }

    public function provideRedirectionExceptionResponses(): array
    {
        return [
            [
                new MockResponse(
                    '',
                    [
                        'http_code' => 301,
                    ]
                ),
                'HTTP 301 returned for "https://superbrave.nl/".',
            ],
            [
                new MockResponse(
                    '<html><head><title>Redirected to: https://superbrave.nl/</title></head><body></body></body></html>',
                    [
                        'http_code' => 301,
                    ]
                ),
                'HTTP 301 returned for "https://superbrave.nl/" with response: <html><head><title>Redirected to: https://superbrave.nl/</title></head><body></body></body></html>',
            ],
            [
                new MockResponse(
                    '<html><head><title>Redirected to: https://superbrave.nl/</title></head><body><h1>Redirected to: https://superbrave.nl/</h1><p>We should have redirected you to another URL, but we did not...</p></body></body></html>',
                    [
                        'http_code' => 301,
                    ]
                ),
                'HTTP 301 returned for "https://superbrave.nl/" with response: <html><head><title>Redirected to: https://superbrave.nl/</title></head><body><h1>Redirected to: https://superbrave.nl/</ (truncated...)',
            ],
        ];
    }
}
