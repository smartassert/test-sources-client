<?php

declare(strict_types=1);

namespace SmartAssert\TestSourcesClient;

use Psr\Http\Message\ResponseInterface;

class UnexpectedHttpResponseException extends \Exception
{
    public function __construct(public readonly ResponseInterface $response)
    {
        parent::__construct($response->getReasonPhrase(), $response->getStatusCode());
    }
}
