<?php

namespace Solarium\Tests;

use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;
use Solarium\Client;

class ClientTest extends TestCase
{
    /**
     * The version tag we use for testing within github actions.
     *
     * @var string
     */
    protected static $versionTag = '76.5.4';

    public static function setUpBeforeClass(): void
    {
        $installedVersion = InstalledVersions::getPrettyVersion('solarium/solarium');

        if ('dev-master' === $installedVersion) {
            self::assertSame($installedVersion, Client::getVersion());
            self::markTestSkipped(sprintf('Testing on %s, skipping tests against version tag %s.', $installedVersion, self::$versionTag));
        }

        parent::setUpBeforeClass();
    }

    public function testGetVersion()
    {
        $this->assertSame(
            self::$versionTag,
            Client::getVersion()
        );
    }

    /**
     * @deprecated The class constant will be removed in Solarium 6.3.0.
     */
    public function testVersionConstant()
    {
        $this->assertSame(
            self::$versionTag,
            Client::VERSION
        );
    }

    public function testCheckExact()
    {
        $this->assertTrue(
            Client::checkExact(self::$versionTag)
        );
    }

    public function testCheckExactPartial()
    {
        $this->assertTrue(
            Client::checkExact(substr(self::$versionTag, 0, 1))
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
            Client::checkMinimal(self::$versionTag)
        );
    }

    public function testCheckMinimalPartial()
    {
        $this->assertTrue(
            Client::checkMinimal(substr(self::$versionTag, 0, 1))
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
