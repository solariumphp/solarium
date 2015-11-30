<?php

namespace Solarium\Tests\Support\DataFixtures;

use Solarium\Support\DataFixtures\FixtureLoader;
use Solarium\Tests\Support\DataFixtures\Fixtures\MockFixture1;

class FixtureLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $fixturePath;

    private $client;

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

    protected function setUp()
    {
        $this->client = $this->getMock('Solarium\Core\Client\ClientInterface');
        $this->fixturePath = __DIR__ . '/Fixtures/';
    }

    private function mockLoader()
    {
        $loader = $this->getMock('Solarium\Support\DataFixtures\Loader', array(), array($this->client));

        $loader->expects($this->once())
            ->method('loadFromDirectory')
            ->with($this->fixturePath);

        $loader->expects($this->once())
            ->method('getFixtures')
            ->will(
                $this->returnValue(
                    array(
                        $this->getMockFixture()
                    )
                )
            );

        return $loader;
    }

    private function mockPurger($expectPurge)
    {
        $purger = $this->getMock('Solarium\Support\DataFixtures\Purger', array(), array($this->client));

        $purger->expects($expectPurge ? $this->once() : $this->never())
            ->method('purge');

        return $purger;
    }

    private function mockExecutor()
    {
        $executor = $this->getMock('Solarium\Support\DataFixtures\Executor', array(), array($this->client));

        return $executor;
    }

    private function getMockFixture()
    {
        return new MockFixture1();
    }
}
