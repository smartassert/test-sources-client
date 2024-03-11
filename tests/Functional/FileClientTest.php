<?php

declare(strict_types=1);

namespace SmartAssert\TestSourcesClient\Tests\Functional;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use SmartAssert\TestSourcesClient\FileClient;
use webignition\HttpHistoryContainer\Container as HttpHistoryContainer;

class FileClientTest extends TestCase
{
    public function testFoo(): void
    {
        $mockHandler = new MockHandler();

        $handlerStack = HandlerStack::create($mockHandler);

        $httpHistoryContainer = new HttpHistoryContainer();
        $handlerStack->push(Middleware::history($httpHistoryContainer));
        $httpClient = new HttpClient(['handler' => $handlerStack]);

        $httpFactory = new HttpFactory();
        $baseUrl = 'https://example.com';

        $client = new FileClient($httpClient, $httpFactory, $httpFactory, $baseUrl);

        $mockHandler->append(new Response());

        $token = md5((string) rand());
        $fileSourceId = md5((string) rand());
        $filename = md5((string) rand()) . '.yaml';
        $content = md5((string) rand());

        $client->add($token, $fileSourceId, $filename, $content);

        $request = $httpHistoryContainer->getTransactions()->getRequests()->getLast();
        \assert($request instanceof RequestInterface);

        self::assertSame('POST', $request->getMethod());
        self::assertSame('Bearer ' . $token, $request->getHeaderLine('authorization'));
        self::assertSame('text/x-yaml', $request->getHeaderLine('content-type'));
        self::assertSame($baseUrl . '/file-source/' . $fileSourceId . '/' . $filename, (string) $request->getUri());
        self::assertSame($content, $request->getBody()->getContents());
    }
}
