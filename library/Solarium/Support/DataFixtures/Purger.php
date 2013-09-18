<?php

namespace Solarium\Support\DataFixtures;

use Solarium\Core\Client\Client;

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
     * @param Client $client
     */
    public function __construct(Client $client)
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
