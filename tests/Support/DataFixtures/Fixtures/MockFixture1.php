<?php

namespace Solarium\Tests\Support\DataFixtures\Fixtures;

use Solarium\Core\Client\ClientInterface;
use Solarium\Support\DataFixtures\FixtureInterface;

class MockFixture1 implements FixtureInterface
{
    /**
     * @param Client $client
     */
    public function load(ClientInterface $client)
    {
        // Not needed in unit test
    }
}
