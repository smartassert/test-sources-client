<?php

declare(strict_types=1);

namespace SmartAssert\TestSourcesClient\Tests\Integration;

use SmartAssert\TestSourcesClient\FileSourceClient;
use Symfony\Component\Uid\Ulid;

class FileSourceClientTest extends AbstractIntegrationTestCase
{
    private static FileSourceClient $fileSourceClient;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$fileSourceClient = new FileSourceClient(
            self::$httpClient,
            self::$httpFactory,
            self::$httpFactory,
            self::BASE_URL
        );
    }

    public function testCreateSuccess(): void
    {
        $fileSourceId = self::$fileSourceClient->create(self::$user1ApiToken, md5((string) rand()));

        self::assertNotNull($fileSourceId);
        self::assertTrue(Ulid::isValid($fileSourceId));
    }
}
