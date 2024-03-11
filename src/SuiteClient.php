<?php

declare(strict_types=1);

namespace SmartAssert\TestSourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface as HttpRequestFactory;
use Psr\Http\Message\StreamFactoryInterface;

readonly class SuiteClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private HttpRequestFactory $httpRequestFactory,
        private StreamFactoryInterface $streamFactory,
        private string $baseUrl,
    ) {
    }

    /**
     * @param non-empty-string $token
     * @param string[]         $tests
     *
     * @return ?non-empty-string
     *
     * @throws ClientExceptionInterface
     * @throws UnexpectedHttpResponseException
     */
    public function create(string $token, string $sourceId, string $label, array $tests): ?string
    {
        $request = $this->httpRequestFactory->createRequest(
            'POST',
            $this->baseUrl . '/suite'
        );

        $body = http_build_query(['source_id' => $sourceId, 'label' => $label, 'tests' => $tests]);

        $request = $request->withHeader('Authorization', 'Bearer ' . $token);
        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');
        $request = $request->withBody($this->streamFactory->createStream($body));

        $response = $this->httpClient->sendRequest($request);

        if (200 !== $response->getStatusCode()) {
            throw new UnexpectedHttpResponseException($response);
        }

        if ('application/json' !== $response->getHeaderLine('content-type')) {
            throw new UnexpectedHttpResponseException($response);
        }

        $responseData = json_decode($response->getBody()->getContents(), true);
        if (!is_array($responseData)) {
            throw new UnexpectedHttpResponseException($response);
        }

        $id = $responseData['id'] ?? '';
        $id = is_string($id) ? $id : '';

        return '' === $id ? null : $id;
    }
}
