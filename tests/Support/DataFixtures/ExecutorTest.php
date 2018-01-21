<?php

namespace Solarium\Tests\Support\DataFixtures;

use Solarium\Support\DataFixtures\Executor;

use PHPUnit\Framework\TestCase;

class ExecutorTest extends TestCase
{
    public function testLoad()
    {
        $solarium = $this->getMock('Solarium\Core\Client\ClientInterface');

        $mockFixtures = array(
            $this->getMockFixture($solarium),
            $this->getMockFixture($solarium),
        );

        $executor = new Executor($solarium);
        $executor->execute($mockFixtures);
    }

    private function getMockFixture($client)
    {
        $fixture = $this->getMock('Solarium\Support\DataFixtures\FixtureInterface');
        $fixture->expects($this->once())
            ->method('load')
            ->with($client);

        return $fixture;
    }
}
