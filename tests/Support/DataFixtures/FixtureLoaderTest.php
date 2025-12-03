<?php

namespace Solarium\Tests\Support\DataFixtures;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\ClientInterface;
use Solarium\Support\DataFixtures\Executor;
use Solarium\Support\DataFixtures\FixtureLoader;
use Solarium\Support\DataFixtures\Loader;
use Solarium\Support\DataFixtures\Purger;
use Solarium\Tests\Support\DataFixtures\Fixtures\MockFixture1;

class FixtureLoaderTest extends TestCase
{
    private string $fixturePath;

    private ClientInterface $client;

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->fixturePath = __DIR__.'/Fixtures/';
    }

    public function testWithAppending(): void
    {
        $loader = $this->mockLoader();
        $purger = $this->mockPurger(false);
        $executor = $this->mockExecutor();

        $fixtureLoader = new FixtureLoader($loader, $purger, $executor);

        $fixtureLoader->loadFixturesFromDir($this->fixturePath);
    }

    public function testWithPurging(): void
    {
        $loader = $this->mockLoader();
        $purger = $this->mockPurger(true);
        $executor = $this->mockExecutor();

        $fixtureLoader = new FixtureLoader($loader, $purger, $executor);

        $fixtureLoader->loadFixturesFromDir($this->fixturePath, false);
    }

    /**
     * @return MockObject&Loader
     */
    private function mockLoader()
    {
        /** @var MockObject&Loader $loader */
        $loader = $this->createMock(Loader::class);

        $loader->expects($this->once())
            ->method('loadFromDirectory')
            ->with($this->fixturePath);

        $loader->expects($this->once())
            ->method('getFixtures')
            ->willReturn(
                [
                    $this->getMockFixture(),
                ]
            );

        return $loader;
    }

    /**
     * @return MockObject&Purger
     */
    private function mockPurger(bool $expectPurge)
    {
        /** @var MockObject&Purger $purger */
        $purger = $this->createMock(Purger::class);

        $purger->expects($expectPurge ? $this->once() : $this->never())
            ->method('purge');

        return $purger;
    }

    /**
     * @return MockObject&Executor
     */
    private function mockExecutor()
    {
        /** @var MockObject&Executor $executor */
        $executor = $this->createMock(Executor::class);

        return $executor;
    }

    private function getMockFixture(): MockFixture1
    {
        return new MockFixture1();
    }
}
