<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Io\ServerRequest;
use React\Http\Response;
use WyriHaximus\React\Http\Middleware\CssCompressMiddleware;
use function Clue\React\Block\await;
use function RingCentral\Psr7\stream_for;

final class CssCompressMiddlewareTest extends TestCase
{
    public function testCompressedResponse()
    {
        $request = (new ServerRequest('GET', 'https://example.com/'))->withBody(stream_for('foo.bar'));
        $middleware = new CssCompressMiddleware();
        /** @var ServerRequestInterface $compressedResponse */
        $compressedResponse = await($middleware($request, function (ServerRequestInterface $request) {
            return new Response(
                200,
                [
                    'Content-Type' => CssCompressMiddleware::MIME_TYPE,
                ],
                'h1 {
                    color: red;
                }'
            );
        }), Factory::create());
        self::assertSame(13, (int)$compressedResponse->getHeaderLine('Content-Length'));
        self::assertSame('h1{color:red}', (string)$compressedResponse->getBody());
    }
}
