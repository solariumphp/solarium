<?php

namespace Solarium\Tests\Support\DataFixtures;

use Solarium\Support\DataFixtures\Loader;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetEmptyFixtures()
    {
        $loader = new Loader();
        $this->assertEmpty($loader->getFixtures());
    }

    public function testAddFixtures()
    {
        $loader = new Loader();

        $fixtures = array(
            $this->getMock('Solarium\Support\DataFixtures\FixtureInterface'),
            $this->getMock('Solarium\Support\DataFixtures\FixtureInterface'),
        );

        foreach ($fixtures as $fixture) {
            $loader->addFixture($fixture);
        }

        $this->assertEquals($fixtures, $loader->getFixtures());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadFromInvalidDir()
    {
        $loader = new Loader();
        $loader->loadFromDirectory('bla');
    }

    public function testLoadFromDir()
    {
        $loader = new Loader();
        $loader->loadFromDirectory(__DIR__ . '/Fixtures/');

        $loadedFixtures = $loader->getFixtures();
        $this->assertCount(3, $loadedFixtures);
        foreach ($loadedFixtures as $fixture) {
            $this->assertInstanceOf('Solarium\Support\DataFixtures\FixtureInterface', $fixture);
        }
    }
}
