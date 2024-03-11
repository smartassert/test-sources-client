<?php

declare(strict_types=1);

namespace SmartAssert\TestSourcesClient\Tests\Integration;

use SmartAssert\TestSourcesClient\GitSourceClient;
use Symfony\Component\Uid\Ulid;

class GitSourceClientTest extends AbstractIntegrationTestCase
{
    private static GitSourceClient $gitSourceClient;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$gitSourceClient = new GitSourceClient(
            self::$httpClient,
            self::$httpFactory,
            self::$httpFactory,
            self::BASE_URL
        );
    }

    public function testCreateSuccess(): void
    {
        $gitSourceId = self::$gitSourceClient->create(
            self::$user1ApiToken,
            md5((string) rand()),
            md5((string) rand()),
            md5((string) rand()),
            null
        );

        self::assertNotNull($gitSourceId);
        self::assertTrue(Ulid::isValid($gitSourceId));
    }
}
