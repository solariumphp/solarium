<?php

namespace Solarium\Tests\Support\DataFixtures;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Support\DataFixtures\FixtureInterface;
use Solarium\Support\DataFixtures\Loader;

class LoaderTest extends TestCase
{
    public function testGetEmptyFixtures()
    {
        $loader = new Loader();
        $this->assertEmpty($loader->getFixtures());
    }

    public function testAddFixtures()
    {
        $loader = new Loader();

        $fixtures = [
            $this->createMock(FixtureInterface::class),
            $this->createMock(FixtureInterface::class),
        ];

        foreach ($fixtures as $fixture) {
            $loader->addFixture($fixture);
        }

        $this->assertSame($fixtures, $loader->getFixtures());
    }

    public function testLoadFromInvalidDir()
    {
        $loader = new Loader();
        $this->expectException(InvalidArgumentException::class);
        $loader->loadFromDirectory('bla');
    }

    public function testLoadFromDir()
    {
        $loader = new Loader();
        $loader->loadFromDirectory(__DIR__.'/Fixtures/');

        $loadedFixtures = $loader->getFixtures();
        $this->assertCount(3, $loadedFixtures);
        foreach ($loadedFixtures as $fixture) {
            $this->assertInstanceOf(FixtureInterface::class, $fixture);
        }
    }
}
