<?php

namespace Solarium\Tests\Support\DataFixtures;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\ClientInterface;
use Solarium\Support\DataFixtures\Executor;
use Solarium\Support\DataFixtures\FixtureLoader;
use Solarium\Support\DataFixtures\Loader;
use Solarium\Support\DataFixtures\Purger;
use Solarium\Tests\Support\DataFixtures\Fixtures\MockFixture1;

class FixtureLoaderTest extends TestCase
{
    private $fixturePath;

    private $client;

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->fixturePath = __DIR__.'/Fixtures/';
    }

    public function testWithAppending()
    {
        $loader = $this->mockLoader();
        $purger = $this->mockPurger(false);
        $executor = $this->mockExecutor();

        $fixtureLoader = new FixtureLoader($loader, $purger, $executor);

        $fixtureLoader->loadFixturesFromDir($this->fixturePath);
    }

    public function testWithPurging()
    {
        $loader = $this->mockLoader();
        $purger = $this->mockPurger(true);
        $executor = $this->mockExecutor();

        $fixtureLoader = new FixtureLoader($loader, $purger, $executor);

        $fixtureLoader->loadFixturesFromDir($this->fixturePath, false);
    }

    private function mockLoader()
    {
        $loader = $this->createMock(Loader::class);

        $loader->expects($this->once())
            ->method('loadFromDirectory')
            ->with($this->fixturePath);

        $loader->expects($this->once())
            ->method('getFixtures')
            ->will(
                $this->returnValue(
                    [
                        $this->getMockFixture(),
                    ]
                )
            );

        return $loader;
    }

    private function mockPurger($expectPurge)
    {
        $purger = $this->createMock(Purger::class);

        $purger->expects($expectPurge ? $this->once() : $this->never())
            ->method('purge');

        return $purger;
    }

    private function mockExecutor()
    {
        $executor = $this->createMock(Executor::class);

        return $executor;
    }

    private function getMockFixture()
    {
        return new MockFixture1();
    }
}
