<?php

namespace Solarium\Tests;

use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;
use Solarium\Client;

class ClientTest extends TestCase
{
    /**
     * @var string
     */
    protected static $installedVersion;

    public static function setUpBeforeClass(): void
    {
        self::$installedVersion = InstalledVersions::getPrettyVersion('solarium/solarium');

        // @see https://semver.org/#is-there-a-suggested-regular-expression-regex-to-check-a-semver-string
        $semverRegex =
            '/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-((?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/'
        ;

        if (!preg_match($semverRegex, self::$installedVersion)) {
            self::assertSame(self::$installedVersion, Client::getVersion());
            self::markTestSkipped(sprintf('Skipping tests against non-semantic version string %s.', self::$installedVersion));
        }

        parent::setUpBeforeClass();
    }

    public function testGetVersion()
    {
        $this->assertSame(
            self::$installedVersion,
            Client::getVersion()
        );
    }

    public function testCheckExact()
    {
        $this->assertTrue(
            Client::checkExact(self::$installedVersion)
        );
    }

    public function testCheckExactPartial()
    {
        $this->assertTrue(
            Client::checkExact(substr(self::$installedVersion, 0, 1))
        );
    }

    public function testCheckExactLower()
    {
        $this->assertFalse(
            Client::checkExact('0.1')
        );
    }

    public function testCheckExactHigher()
    {
        $this->assertFalse(
            Client::checkExact('99.0')
        );
    }

    public function testCheckMinimal()
    {
        $this->assertTrue(
            Client::checkMinimal(self::$installedVersion)
        );
    }

    public function testCheckMinimalPartial()
    {
        $this->assertTrue(
            Client::checkMinimal(substr(self::$installedVersion, 0, 1))
        );
    }

    public function testCheckMinimalLower()
    {
        $this->assertTrue(
            Client::checkMinimal('0.1.0')
        );
    }

    public function testCheckMinimalHigher()
    {
        $this->assertFalse(
            Client::checkMinimal('99.0')
        );
    }
}
