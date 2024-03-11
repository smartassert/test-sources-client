<?php

declare(strict_types=1);

namespace Functional;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use SmartAssert\TestSourcesClient\GitSourceClient;
use webignition\HttpHistoryContainer\Container as HttpHistoryContainer;

class GitSourceClientTest extends TestCase
{
    public function testFoo(): void
    {
        $mockHandler = new MockHandler();

        $handlerStack = HandlerStack::create($mockHandler);

        $httpHistoryContainer = new HttpHistoryContainer();
        $handlerStack->push(Middleware::history($httpHistoryContainer));
        $httpClient = new Client(['handler' => $handlerStack]);

        $httpFactory = new HttpFactory();
        $baseUrl = 'https://example.com';

        $client = new GitSourceClient($httpClient, $httpFactory, $httpFactory, $baseUrl);

        $mockHandler->append(new Response(
            200,
            ['content-type' => 'application/json'],
            (string) json_encode([
                'id' => md5((string) rand()),
            ])
        ));

        $token = md5((string) rand());
        $label = md5((string) rand());
        $hostUrl = md5((string) rand());
        $path = md5((string) rand());
        $credentials = md5((string) rand());

        $client->create($token, $label, $hostUrl, $path, $credentials);

        $request = $httpHistoryContainer->getTransactions()->getRequests()->getLast();
        \assert($request instanceof RequestInterface);

        self::assertSame('POST', $request->getMethod());
        self::assertSame('Bearer ' . $token, $request->getHeaderLine('authorization'));
        self::assertSame('application/x-www-form-urlencoded', $request->getHeaderLine('content-type'));
        self::assertSame($baseUrl . '/git-source', (string) $request->getUri());
        self::assertSame(
            http_build_query([
                'type' => 'git',
                'label' => $label,
                'host-url' => $hostUrl,
                'path' => $path,
                'credentials' => $credentials,
            ]),
            $request->getBody()->getContents()
        );
    }
}
