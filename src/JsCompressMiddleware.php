<?php

declare(strict_types=1);

namespace WyriHaximus\React\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Io\HttpBodyStream;
use React\Promise\PromiseInterface;
use WyriHaximus\HtmlCompress\Factory;
use WyriHaximus\HtmlCompress\HtmlCompressorInterface;

use function explode;
use function React\Promise\resolve;
use function RingCentral\Psr7\stream_for;
use function Safe\substr;
use function strlen;

final class JsCompressMiddleware
{
    public const MIME_TYPE = 'application/javascript';

    private HtmlCompressorInterface $compressor;

    /**
     * @phpstan-ignore-next-line
     */
    public function __construct(?HtmlCompressorInterface $compressor = null)
    {
        if ($compressor === null) {
            $compressor = Factory::construct();
        }

        $this->compressor = $compressor;
    }

    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface
    {
        $response = $next($request);

        if (! ($response instanceof PromiseInterface)) {
            return resolve($this->handleResponse($response));
        }

        return $response->then(fn (ResponseInterface $response): ResponseInterface => $this->handleResponse($response));
    }

    private function handleResponse(ResponseInterface $response): ResponseInterface
    {
        if ($response->getBody() instanceof HttpBodyStream) {
            return $response;
        }

        if (! $response->hasHeader('content-type')) {
            return $response;
        }

        [$contentType] = explode(';', $response->getHeaderLine('content-type'));
        if ($contentType !== self::MIME_TYPE) {
            return $response;
        }

        $body           = (string) $response->getBody();
        $compressedBody = substr($this->compressor->compress('<script>' . $body . '</script>'), 8, -9);

        return $response->withBody(stream_for($compressedBody))->withHeader('Content-Length', (string) strlen($compressedBody));
    }
}
