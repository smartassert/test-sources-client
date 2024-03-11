<?php

declare(strict_types=1);

namespace SmartAssert\TestSourcesClient\Tests\Integration;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface as HttpClientInterfaceAlias;
use Psr\Http\Message\RequestFactoryInterface as HttpRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface as HttpStreamFactoryInterfaceAlias;
use SmartAssert\TestAuthenticationProviderBundle\ApiKeyProvider;
use SmartAssert\TestAuthenticationProviderBundle\ApiTokenProvider;
use SmartAssert\TestAuthenticationProviderBundle\FrontendTokenProvider;

abstract class AbstractIntegrationTestCase extends TestCase
{
    protected const string BASE_URL = 'http://localhost:9081';
    protected const string USER1_EMAIL = 'user1@example.com';
    protected const string USER1_PASSWORD = 'password';
    protected const string USER2_EMAIL = 'user2@example.com';
    protected const string USER2_PASSWORD = 'password';

    /**
     * @var non-empty-string
     */
    protected static string $user1ApiToken;

    /**
     * @var non-empty-string
     */
    protected static string $user2ApiToken;

    protected static HttpClientInterfaceAlias $httpClient;

    protected static HttpRequestFactoryInterface&HttpStreamFactoryInterfaceAlias $httpFactory;

    public static function setUpBeforeClass(): void
    {
        self::$user1ApiToken = self::createUserApiToken(self::USER1_EMAIL);
        self::$user2ApiToken = self::createUserApiToken(self::USER2_EMAIL);

        self::$httpClient = new HttpClient();
        self::$httpFactory = new HttpFactory();
    }

    /**
     * @param non-empty-string $email
     *
     * @return non-empty-string
     */
    protected static function createUserApiToken(string $email): string
    {
        $usersBaseUrl = 'http://localhost:9080';
        $httpClient = new HttpClient();

        $frontendTokenProvider = new FrontendTokenProvider(
            [
                self::USER1_EMAIL => self::USER1_PASSWORD,
                self::USER2_EMAIL => self::USER2_PASSWORD,
            ],
            $usersBaseUrl,
            $httpClient
        );
        $apiKeyProvider = new ApiKeyProvider($usersBaseUrl, $httpClient, $frontendTokenProvider);
        $apiTokenProvider = new ApiTokenProvider($usersBaseUrl, $httpClient, $apiKeyProvider);

        return $apiTokenProvider->get($email);
    }
}
