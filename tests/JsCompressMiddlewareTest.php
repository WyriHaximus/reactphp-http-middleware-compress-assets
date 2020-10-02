<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use WyriHaximus\React\Http\Middleware\JsCompressMiddleware;
use function Clue\React\Block\await;
use function RingCentral\Psr7\stream_for;

final class JsCompressMiddlewareTest extends TestCase
{
    public function testCompressedResponse()
    {
        $request = (new ServerRequest('GET', 'https://example.com/'))->withBody(stream_for('foo.bar'));
        $middleware = new JsCompressMiddleware();
        /** @var ServerRequestInterface $compressedResponse */
        $compressedResponse = await($middleware($request, function (ServerRequestInterface $request) {
            return new Response(
                200,
                [
                    'Content-Type' => JsCompressMiddleware::MIME_TYPE,
                ],
                '
                    window.wyriMapsNetGetChunkURL = function () {
                        return \'https://www.wyrimaps.net/\';
                    };'
            );
        }), Factory::create());
        self::assertSame(77, (int)$compressedResponse->getHeaderLine('Content-Length'));
        self::assertSame(';window.wyriMapsNetGetChunkURL=function(){return\'https://www.wyrimaps.net/\'};', (string)$compressedResponse->getBody());
    }
}
