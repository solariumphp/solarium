<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
    public function execute(array $fixtures): void
    {
        foreach ($fixtures as $fixture) {
            $fixture->load($this->client);
        }
    }
}
