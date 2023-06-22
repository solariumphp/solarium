<?php

namespace Solarium\Tests\Integration;

use Solarium\Core\Client\State\ClusterState;

/**
 * Abstract base class.
 */
abstract class AbstractCloudTestCase extends AbstractTechproductsTestCase
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
                    'username' => 'solr',
                    'password' => 'SolrRocks',
                ],
            ],
        ];

        self::$client = TestClientFactory::createWithPsr18Adapter(self::$config);

        // upload the techproducts configset
        $configsetsQuery = self::$client->createConfigsets();
        $action = $configsetsQuery->createUpload();
        $action
            ->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'techproducts.zip')
            ->setName('techproducts')
            ->setOverwrite(true);
        $configsetsQuery->setAction($action);
        $response = self::$client->configsets($configsetsQuery);
        static::assertTrue($response->getWasSuccessful());

        // create collection with unique name using the techproducts configset
        $collectionsQuery = self::$client->createCollections();
        $createAction = $collectionsQuery->createCreate();
        $createAction->setName(self::$name)
            ->setCollectionConfigName('techproducts')
            // @todo if we set a lower number of shards compared to the number of nodes we get random test failures for
            //       the managed resources tests. (Managed resources are deprecated in Solr 8 anyway.) These failures
            //       only happen with the HttpAdapter and Psr18Adapter. The CurlAdapter has a higher timeout especially
            //       for the integration tests which might be the reason.
            ->setNumShards(3);
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

        // now we delete the configsets we created in setUpBeforeClass() and during the tests
        $configsetsQuery = self::$client->createConfigsets();
        $action = $configsetsQuery->createDelete();
        $action->setName('copy_of_techproducts');
        $configsetsQuery->setAction($action);
        $result = self::$client->configsets($configsetsQuery);
        static::assertTrue($result->getWasSuccessful());
        $action->setName('techproducts');
        $result = self::$client->configsets($configsetsQuery);
        static::assertTrue($result->getWasSuccessful());
    }

    public function testConfigsetsApi()
    {
        $configsetsQuery = self::$client->createConfigsets();

        $action = $configsetsQuery->createList();
        $configsetsQuery->setAction($action);
        $result = self::$client->configsets($configsetsQuery);
        $this->assertTrue($result->getWasSuccessful());

        $this->assertContains('_default', $result->getConfigSets());
        $this->assertContains('techproducts', $result->getConfigSets());
        $this->assertNotContains('copy_of_techproducts', $result->getConfigSets());

        $action = $configsetsQuery->createCreate();
        $action->setName('copy_of_techproducts')->setBaseConfigSet('techproducts');
        $configsetsQuery->setAction($action);
        $result = self::$client->configsets($configsetsQuery);
        $this->assertTrue($result->getWasSuccessful());

        $action = $configsetsQuery->createList();
        $configsetsQuery->setAction($action);
        $result = self::$client->configsets($configsetsQuery);
        $this->assertTrue($result->getWasSuccessful());
        $this->assertContains('_default', $result->getConfigSets());
        $this->assertContains('techproducts', $result->getConfigSets());
        $this->assertContains('copy_of_techproducts', $result->getConfigSets());
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
        $this->assertCount(3, $clusterState->getLiveNodes());
        $this->assertCount(1, $clusterState->getCollections());
        $this->assertTrue($clusterState->collectionExists(self::$name));
    }
}
