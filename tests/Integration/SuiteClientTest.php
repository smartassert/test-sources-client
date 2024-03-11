<?php

declare(strict_types=1);

namespace SmartAssert\TestSourcesClient\Tests\Integration;

use SmartAssert\TestSourcesClient\FileSourceClient;
use SmartAssert\TestSourcesClient\SuiteClient;
use Symfony\Component\Uid\Ulid;

class SuiteClientTest extends AbstractIntegrationTestCase
{
    private static SuiteClient $suiteClient;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$suiteClient = new SuiteClient(
            self::$httpClient,
            self::$httpFactory,
            self::$httpFactory,
            self::BASE_URL
        );
    }

    public function testCreateSuccess(): void
    {
        $fileSourceClient = new FileSourceClient(
            self::$httpClient,
            self::$httpFactory,
            self::$httpFactory,
            self::BASE_URL
        );

        $fileSourceId = $fileSourceClient->create(self::$user1ApiToken, md5((string) rand()));
        \assert(is_string($fileSourceId));

        $suiteId = self::$suiteClient->create(
            self::$user1ApiToken,
            $fileSourceId,
            md5((string) rand()),
            []
        );

        self::assertNotNull($suiteId);
        self::assertTrue(Ulid::isValid($suiteId));
    }
}
