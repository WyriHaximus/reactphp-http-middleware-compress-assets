<?php

declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use WyriHaximus\React\Http\Middleware\CssCompressMiddleware;

use function RingCentral\Psr7\stream_for;

final class CssCompressMiddlewareTest extends AsyncTestCase
{
    /**
     * @test
     */
    public function testCompressedResponse(): void
    {
        $request            = (new ServerRequest('GET', 'https://example.com/'))->withBody(stream_for('foo.bar'));
        $middleware         = new CssCompressMiddleware();
        $compressedResponse = $this->await($middleware($request, static fn (ServerRequestInterface $request): ResponseInterface => new Response(
            200,
            [
                'Content-Type' => CssCompressMiddleware::MIME_TYPE,
            ],
            'h1 {
                color: red;
            }'
        )));
        self::assertSame(13, (int) $compressedResponse->getHeaderLine('Content-Length'));
        self::assertSame('h1{color:red}', (string) $compressedResponse->getBody());
    }
}
