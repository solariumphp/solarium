<?php

namespace Solarium\Support\DataFixtures;

use Solarium\Core\Client\ClientInterface;

/**
 * @author Baldur Rensch <brensch@gmail.com>
 */
interface FixtureInterface
{
    /**
     * @param ClientInterface $client
     */
    public function load(ClientInterface $client);
}
