<?php

namespace Solarium\Tests\Support\DataFixtures;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\ClientInterface;
use Solarium\Support\DataFixtures\Executor;
use Solarium\Support\DataFixtures\FixtureInterface;

class ExecutorTest extends TestCase
{
    public function testLoad()
    {
        $client = $this->createMock(ClientInterface::class);

        $mockFixtures = [
            $this->getMockFixture($client),
            $this->getMockFixture($client),
        ];

        $executor = new Executor($client);
        $executor->execute($mockFixtures);
    }

    private function getMockFixture($client)
    {
        $fixture = $this->createMock(FixtureInterface::class);
        $fixture->expects($this->once())
            ->method('load')
            ->with($client);

        return $fixture;
    }
}
