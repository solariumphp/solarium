<?php

namespace Solarium\Tests\Support\DataFixtures\Fixtures;

use Solarium\Core\Client\Client;
use Solarium\Support\DataFixtures\FixtureInterface;

class MockFixture2 implements FixtureInterface
{
    /**
     * @param Client $client
     */
    public function load(Client $client)
    {
        // Not needed in unit test
    }
}

class MockFixture3 implements FixtureInterface
{
    /**
     * @param Client $client
     */
    public function load(Client $client)
    {
        // Not needed in unit test
    }
}
