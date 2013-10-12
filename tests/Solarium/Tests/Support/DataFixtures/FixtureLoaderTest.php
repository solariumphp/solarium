<?php

namespace Solarium\Tests\Support\DataFixtures;

use Solarium\Support\DataFixtures\FixtureLoader;

class FixtureLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $fixturePath;

    private $client;

    public function testWithAppending()
    {
        $loader = $this->mockLoader();
        $purger = $this->mockPurger(false);

        $fixtureLoader = new FixtureLoader($loader, $purger);

        $fixtureLoader->loadFixturesFromDir($this->fixturePath);
    }

    public function testWithPurging()
    {
        $loader = $this->mockLoader();
        $purger = $this->mockPurger(true);

        $fixtureLoader = new FixtureLoader($loader, $purger);

        $fixtureLoader->loadFixturesFromDir($this->fixturePath, false);
    }

    protected function setUp()
    {
        $this->client = $this->getMock('Solarium\Core\Client\Client');
        $this->fixturePath = __DIR__ . '/Fixtures/';
    }

    private function mockLoader()
    {
        $loader = $this->getMock('Solarium\Support\DataFixtures\Loader', array(), array($this->client));

        $loader->expects($this->once())
            ->method('loadFromDirectory')
            ->with($this->fixturePath);

        return $loader;
    }

    private function mockPurger($expectPurge)
    {
        $purger = $this->getMock('Solarium\Support\DataFixtures\Purger', array(), array($this->client));

        $purger->expects($expectPurge ? $this->once() : $this->never())
            ->method('purge');

        return $purger;
    }
}
