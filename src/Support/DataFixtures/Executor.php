<?php

namespace Solarium\Support\DataFixtures;

use Solarium\Core\Client\ClientInterface;

/**
 * @author Baldur Rensch <brensch@gmail.com>
 */
class Executor
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * Constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param FixtureInterface[] $fixtures
     */
    public function execute(array $fixtures)
    {
        foreach ($fixtures as $fixture) {
            $fixture->load($this->client);
        }
    }
}
