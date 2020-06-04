<?php

namespace Solarium\Tests\Integration;

use Solarium\Core\Client\State\ClusterState;

/**
 * Abstract base class.
 */
abstract class AbstractCloudTest extends AbstractTechproductsTest
{
    protected static function createTechproducts(): void
    {
        self::$config = [
            'endpoint' => [
                'localhost' => [
                    'host' => '127.0.0.1',
                    'port' => 8983,
                    'path' => '/',
                    'collection' => self::$name,
                ],
            ],
        ];

        self::$client = TestClientFactory::createWithPsr18Adapter(self::$config);

        $collectionsQuery = self::$client->createCollections();

        // create core with unique name using the techproducts configset
        $createAction = $collectionsQuery->createCreate();
        $createAction->setName(self::$name)
            ->setCollectionConfigName('techproducts')
            ->setNumShards(2);
        $collectionsQuery->setAction($createAction);
        $response = self::$client->collections($collectionsQuery);
        static::assertTrue($response->getWasSuccessful());
    }

    public static function tearDownAfterClass(): void
    {
        $collectionsQuery = self::$client->createCollections();

        // now we delete the collection we created in setUpBeforeClass()
        $deleteAction = $collectionsQuery->createDelete();
        $deleteAction->setName(self::$name);
        $collectionsQuery->setAction($deleteAction);
        $response = self::$client->collections($collectionsQuery);
        static::assertTrue($response->getWasSuccessful());
    }

    public function testCreateDelete()
    {
        $collectionsQuery = self::$client->createCollections();

        $action = $collectionsQuery->createCreate();
        $action->setName('test');
        $action->setNumShards(1);
        $collectionsQuery->setAction($action);
        $result = self::$client->collections($collectionsQuery);
        $this->assertTrue($result->getWasSuccessful());

        $action = $collectionsQuery->createDelete();
        $action->setName('test');
        $collectionsQuery->setAction($action);
        $result = self::$client->collections($collectionsQuery);
        $this->assertTrue($result->getWasSuccessful());
    }

    public function testReload()
    {
        $collectionsQuery = self::$client->createCollections();

        $action = $collectionsQuery->createReload();
        $action->setName(self::$name);
        $collectionsQuery->setAction($action);
        $result = self::$client->collections($collectionsQuery);
        $this->assertTrue($result->getWasSuccessful());
    }

    public function testClusterStatus()
    {
        $collectionsQuery = self::$client->createCollections();

        $action = $collectionsQuery->createClusterStatus();
        $collectionsQuery->setAction($action);
        $result = self::$client->collections($collectionsQuery);
        $this->assertTrue($result->getWasSuccessful());
        $clusterState = $result->getClusterState();
        $this->assertSame(ClusterState::class, get_class($clusterState));
        $this->assertCount(2, $clusterState->getLiveNodes());
        $this->assertCount(1, $clusterState->getCollections());
        $this->assertTrue($clusterState->collectionExists(self::$name));
    }
}
