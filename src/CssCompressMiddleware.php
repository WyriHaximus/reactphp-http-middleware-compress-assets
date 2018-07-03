<?php declare(strict_types=1);

namespace WyriHaximus\React\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Io\HttpBodyStream;
use React\Promise\PromiseInterface;
use WyriHaximus\HtmlCompress\Factory;
use WyriHaximus\HtmlCompress\Parser;
use function React\Promise\resolve;
use function RingCentral\Psr7\stream_for;

final class CssCompressMiddleware
{
    const MIME_TYPE = 'text/css';

    /**
     * @var Parser
     */
    private $compressor;

    /**
     * @param Parser $compressor
     */
    public function __construct(Parser $compressor = null)
    {
        if ($compressor === null) {
            $compressor = Factory::constructSmallest(false);
        }

        $this->compressor = $compressor;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $response = $next($request);

        if (!($response instanceof PromiseInterface)) {
            return resolve($this->handleResponse($response));
        }

        return $response->then(function (ResponseInterface $response) {
            return $this->handleResponse($response);
        });
    }

    private function handleResponse(ResponseInterface $response)
    {
        if ($response->getBody() instanceof HttpBodyStream) {
            return $response;
        }

        if (!$response->hasHeader('content-type')) {
            return $response;
        }

        list($contentType) = explode(';', $response->getHeaderLine('content-type'));
        if ($contentType !== self::MIME_TYPE) {
            return $response;
        }

        $body = (string)$response->getBody();
        $compressedBody = substr($this->compressor->compress('<style>' . $body . '</style>'), 7, -8);

        return $response->withBody(stream_for($compressedBody))->withHeader('Content-Length', strlen($compressedBody));
    }
}
