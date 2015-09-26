<?php

namespace Solarium\Support\DataFixtures;

use Solarium\Core\Client\ClientInterface;

/**
 * DataFixtures Purger.
 */
class Purger
{
    /**
     * @var Client
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
    public function purge()
    {
        $update = $this->client->createUpdate();

        $update->addDeleteQuery($this->deleteQuery);
        $update->addCommit();

        $result = $this->client->update($update);

        return 0 == $result->getStatus();
    }
}
