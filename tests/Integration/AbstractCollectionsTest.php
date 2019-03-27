<?php

namespace Solarium\Tests\Integration;

use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\State\ClusterState;
use Solarium\QueryType\Server\Collections\Query\Query;

/**
 * Abstract base class CollectionsTest.
 */
abstract class AbstractCollectionsTest extends AbstractTechproductsTest
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var ClientInterface
     */
    protected $client;

    protected $collection = 'techproducts';

    public function setUp()
    {
        $config = [
            'endpoint' => [
                'localhost' => [
                    'host' => '127.0.0.1',
                    'port' => 8983,
                    'path' => '/',
                    'collection' => $this->collection,
                ],
            ],
            // Curl is the default adapter.
            //'adapter' => 'Solarium\Core\Client\Adapter\Curl',
        ];

        $this->client = new \Solarium\Client($config);

        try {
            $ping = $this->client->createPing();
            $this->client->ping($ping);
        } catch (\Exception $e) {
            $this->markTestSkipped('SolrCloud techproducts example not reachable.');
        }

        $this->query = $this->client->createCollections();
    }

    public function testCreateDelete()
    {
        $action = $this->query->createCreate();
        $action->setName('test');
        $action->setNumShards(1);
        $this->query->setAction($action);
        $result = $this->client->collections($this->query);
        $this->assertTrue($result->getWasSuccessful());

        $action = $this->query->createDelete();
        $action->setName('test');
        $this->query->setAction($action);
        $result = $this->client->collections($this->query);
        $this->assertTrue($result->getWasSuccessful());
    }

    public function testReload()
    {
        $action = $this->query->createReload();
        $action->setName($this->collection);
        $this->query->setAction($action);
        $result = $this->client->collections($this->query);
        $this->assertTrue($result->getWasSuccessful());
    }

    public function testClusterStatus()
    {
        $action = $this->query->createClusterStatus();
        $this->query->setAction($action);
        $result = $this->client->collections($this->query);
        $this->assertTrue($result->getWasSuccessful());
        $clusterState = $result->getClusterState();
        $this->assertSame(ClusterState::class, get_class($clusterState));
        $this->assertCount(2, $clusterState->getLiveNodes());
        $this->assertCount(1, $clusterState->getCollections());
        $this->assertTrue($clusterState->collectionExists('techproducts'));
    }
}
