<?php

namespace Solarium\Tests\Integration\SolrCloud;

use Solarium\QueryType\Server\Collections\Query\Query;
use Solarium\QueryType\Server\Collections\Result\ClusterState;

/**
 * Class CollectionsTest
 * @package Solarium\Tests\Integration\SolrCloud
 * @group integration
 */
class CollectionsTest extends AbstractSolrCloudTest
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp()
    {
        parent::setUp();
        // The default timeout of solarium of 5s seems to be too aggressive on travis and causes random test failures.
        // Set it to the PHP default of 13s.
        $this->client->getEndpoint()->setTimeout(CURLOPT_TIMEOUT);
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
        $this->assertTrue($clusterState->collectionExists('gettingstarted'));
    }
}