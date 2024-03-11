<?php

declare(strict_types=1);

namespace SmartAssert\TestSourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface as HttpRequestFactory;
use Psr\Http\Message\StreamFactoryInterface;

readonly class FileClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private HttpRequestFactory $httpRequestFactory,
        private StreamFactoryInterface $streamFactory,
        private string $baseUrl,
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     * @throws UnexpectedHttpResponseException
     */
    public function add(string $token, string $fileSourceId, string $filename, string $content): void
    {
        $request = $this->httpRequestFactory->createRequest(
            'POST',
            $this->baseUrl . '/file-source/' . $fileSourceId . '/' . $filename
        );

        $request = $request->withHeader('Authorization', 'Bearer ' . $token);
        $request = $request->withHeader('Content-Type', 'text/x-yaml');
        $request = $request->withBody($this->streamFactory->createStream($content));

        $response = $this->httpClient->sendRequest($request);

        if (200 !== $response->getStatusCode()) {
            throw new UnexpectedHttpResponseException($response);
        }
    }
}
