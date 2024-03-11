<?php

declare(strict_types=1);

namespace SmartAssert\TestSourcesClient\Tests\Integration;

use SmartAssert\TestSourcesClient\FileClient;
use SmartAssert\TestSourcesClient\FileSourceClient;

class FileClientTest extends AbstractIntegrationTestCase
{
    private static FileClient $fileClient;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$fileClient = new FileClient(
            self::$httpClient,
            self::$httpFactory,
            self::$httpFactory,
            self::BASE_URL,
        );
    }

    public function testAddSuccess(): void
    {
        $fileSourceClient = new FileSourceClient(
            self::$httpClient,
            self::$httpFactory,
            self::$httpFactory,
            self::BASE_URL
        );

        $fileSourceId = $fileSourceClient->create(self::$user1ApiToken, md5((string) rand()));
        \assert(is_string($fileSourceId));

        self::$fileClient->add(
            self::$user1ApiToken,
            $fileSourceId,
            md5((string) rand()) . '.yaml',
            md5((string) rand())
        );

        self::expectNotToPerformAssertions();
    }
}
