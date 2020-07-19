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
 * DataFixtures Purger.
 */
class Purger
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $deleteQuery = '*:*';

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return bool
     */
    public function purge(): bool
    {
        $update = $this->client->createUpdate();

        $update->addDeleteQuery($this->deleteQuery);
        $update->addCommit();

        $result = $this->client->update($update);

        return 0 === $result->getStatus();
    }
}
