<?php

declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use WyriHaximus\React\Http\Middleware\JsCompressMiddleware;

use function RingCentral\Psr7\stream_for;

final class JsCompressMiddlewareTest extends AsyncTestCase
{
    /**
     * @test
     */
    public function compressedResponse(): void
    {
        $request            = (new ServerRequest('GET', 'https://example.com/'))->withBody(stream_for('foo.bar'));
        $middleware         = new JsCompressMiddleware();
        $compressedResponse = $this->await($middleware($request, static fn (ServerRequestInterface $request): ResponseInterface => new Response(
            200,
            [
                'Content-Type' => JsCompressMiddleware::MIME_TYPE,
            ],
            '
                window.wyriMapsNetGetChunkURL = function () {
                    return \'https://www.wyrimaps.net/\';
                };'
        )));
        self::assertSame(75, (int) $compressedResponse->getHeaderLine('Content-Length'));
        self::assertSame('window.wyriMapsNetGetChunkURL=function(){return\'https://www.wyrimaps.net/\'}', (string) $compressedResponse->getBody());
    }
}
