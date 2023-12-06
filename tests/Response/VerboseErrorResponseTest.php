<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClient\Tests\Response;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Superbrave\VerboseErrorHttpClient\Exception\ClientException;
use Superbrave\VerboseErrorHttpClient\Exception\RedirectionException;
use Superbrave\VerboseErrorHttpClient\Exception\ServerException;
use Superbrave\VerboseErrorHttpClient\Response\VerboseErrorResponse;
use Symfony\Component\HttpClient\Exception\ClientException as SymfonyClientException;
use Symfony\Component\HttpClient\Exception\RedirectionException as SymfonyRedirectionException;
use Symfony\Component\HttpClient\Exception\ServerException as SymfonyServerException;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class VerboseErrorResponseTest extends TestCase
{
    private VerboseErrorResponse $response;

    private MockObject $wrappedResponseMock;

    /**
     * Creates a new VerboseErrorResponse instance for testing.
     */
    protected function setUp(): void
    {
        $this->wrappedResponseMock = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $this->response = new VerboseErrorResponse($this->wrappedResponseMock);
    }

    /**
     * Tests if VerboseErrorResponse::getStatusCode calls the getStatusCode method of the wrapped response.
     */
    public function testGetStatusCode(): void
    {
        $this->wrappedResponseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->assertSame(200, $this->response->getStatusCode());
    }

    /**
     * Tests if VerboseErrorResponse::getInfo calls the getInfo method of the wrapped response.
     */
    public function testGetInfo(): void
    {
        $this->wrappedResponseMock->expects($this->once())
            ->method('getInfo')
            ->with('http_code')
            ->willReturn(200);

        $this->assertSame(200, $this->response->getInfo('http_code'));
    }

    /**
     * Tests if VerboseErrorResponse::getHeaders calls the getHeaders method of the wrapped response and
     * wraps/decorates thrown errors.
     *
     * @dataProvider provideExceptionTestCases
     */
    public function testGetHeaders(
        bool $throw,
        ?HttpExceptionInterface $exception,
        int $httpCode,
        ?string $expectedExceptionClass,
        ?string $expectedExceptionMessage
    ): void {
        $method = $this->wrappedResponseMock->expects($this->once())
            ->method('getHeaders')
            ->with($throw)
            ->willReturn([]);

        if ($exception !== null) {
            $this->wrappedResponseMock->expects($this->exactly(3))
                ->method('getInfo')
                ->willReturnOnConsecutiveCalls(
                    $httpCode,
                    'http://superbrave.nl',
                    []
                );

            $method->willThrowException($exception);

            $this->expectException($expectedExceptionClass);
            $this->expectExceptionCode($httpCode);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $this->response->getHeaders($throw);
    }

    /**
     * Tests if VerboseErrorResponse::getContent calls the getContent method of the wrapped response and
     * wraps/decorates thrown errors.
     *
     * @dataProvider provideExceptionTestCases
     */
    public function testGetContent(
        bool $throw,
        ?HttpExceptionInterface $exception,
        int $httpCode,
        ?string $expectedExceptionClass,
        ?string $expectedExceptionMessage
    ): void {
        $method = $this->wrappedResponseMock->expects($this->atLeastOnce())
            ->method('getContent')
            ->willReturn('');

        if ($exception !== null) {
            $this->wrappedResponseMock->expects($this->exactly(3))
                ->method('getInfo')
                ->willReturnOnConsecutiveCalls(
                    $httpCode,
                    'http://superbrave.nl',
                    []
                );

            $method->willReturnCallback(function ($throw) use ($exception) {
                if ($throw) {
                    throw $exception;
                }

                return '';
            });

            $this->expectException($expectedExceptionClass);
            $this->expectExceptionCode($httpCode);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $this->response->getContent($throw);
    }

    /**
     * Tests if VerboseErrorResponse::toArray calls the toArray method of the wrapped response and
     * wraps/decorates thrown errors.
     *
     * @dataProvider provideExceptionTestCases
     */
    public function testToArray(
        bool $throw,
        ?HttpExceptionInterface $exception,
        int $httpCode,
        ?string $expectedExceptionClass,
        ?string $expectedExceptionMessage
    ): void {
        $method = $this->wrappedResponseMock->expects($this->once())
            ->method('toArray')
            ->with($throw)
            ->willReturn([]);

        if ($exception !== null) {
            $this->wrappedResponseMock->expects($this->exactly(3))
                ->method('getInfo')
                ->willReturnOnConsecutiveCalls(
                    $httpCode,
                    'http://superbrave.nl',
                    []
                );

            $method->willThrowException($exception);

            $this->expectException($expectedExceptionClass);
            $this->expectExceptionCode($httpCode);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $this->response->toArray($throw);
    }

    /**
     * Tests if VerboseErrorResponse::cancel calls the cancel method of the wrapped response.
     */
    public function testCancel(): void
    {
        $this->wrappedResponseMock->expects($this->once())
            ->method('cancel');

        $this->response->cancel();
    }

    /**
     * Returns the exception test cases.
     */
    public static function provideExceptionTestCases(): array
    {
        $response = new MockResponse(
            '',
            [
                'response_headers' => [
                    'content-type' => 'text/html',
                ],
            ]
        );

        return [
            [
                true,
                new SymfonyServerException($response),
                500,
                ServerException::class,
                'HTTP 500 returned for "http://superbrave.nl".',
            ],
            [
                true,
                new SymfonyClientException($response),
                400,
                ClientException::class,
                'HTTP 400 returned for "http://superbrave.nl".',
            ],
            [
                true,
                new SymfonyRedirectionException($response),
                301,
                RedirectionException::class,
                'HTTP 301 returned for "http://superbrave.nl".',
            ],
            [
                false,
                null,
                0,
                null,
                null,
            ],
        ];
    }
}
