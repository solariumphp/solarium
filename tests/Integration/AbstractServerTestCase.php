<?php

namespace Solarium\Tests\Integration;

use Solarium\Exception\HttpException;

abstract class AbstractServerTestCase extends AbstractTechproductsTestCase
{
    protected static function createTechproducts(): void
    {
        self::$config = [
            'endpoint' => [
                'localhost' => [
                    'host' => '127.0.0.1',
                    'port' => 8983,
                    'path' => '/',
                    'core' => self::$name,
                ],
            ],
        ];

        self::$client = TestClientFactory::createWithPsr18Adapter(self::$config);

        $coreAdminQuery = self::$client->createCoreAdmin();

        // create core with unique name using the techproducts configset
        $createAction = $coreAdminQuery->createCreate();
        $createAction->setCore(self::$name)
            ->setConfigSet('solarium');
        $coreAdminQuery->setAction($createAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        static::assertTrue($response->getWasSuccessful());
    }

    public static function tearDownAfterClass(): void
    {
        $coreAdminQuery = self::$client->createCoreAdmin();

        // now we unload the core we created in setUpBeforeClass()
        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore(self::$name)
            ->setDeleteDataDir(true)
            ->setDeleteIndex(true)
            ->setDeleteInstanceDir(true);
        $coreAdminQuery->setAction($unloadAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        static::assertTrue($response->getWasSuccessful());
    }

    public function testCanReloadCore()
    {
        $coreAdminQuery = self::$client->createCoreAdmin();
        $reloadAction = $coreAdminQuery->createReload();
        $reloadAction->setCore(self::$name);
        $coreAdminQuery->setAction($reloadAction);

        $result = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($result->getWasSuccessful());

        // reloading a non existing core should not be successful
        $this->expectException(HttpException::class);
        $reloadAction2 = $coreAdminQuery->createReload();
        $reloadAction2->setCore('nonExistingCore');
        $coreAdminQuery->setAction($reloadAction2);
        self::$client->coreAdmin($coreAdminQuery);
    }

    public function testCoreAdminStatus()
    {
        $coreAdminQuery = self::$client->createCoreAdmin();
        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore(self::$name);

        $coreAdminQuery->setAction($statusAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $json = json_decode($response->getResponse()->getBody());
        $this->assertTrue($response->getWasSuccessful());
        $this->assertGreaterThanOrEqual(0, $json->responseHeader->QTime);
        $this->assertNotNull($response->getStatusResult()->getUptime());
        $this->assertGreaterThan(0, $response->getStatusResult()->getStartTime()->format('U'));
        // lastModified is either null for a very fresh core or a valid DateTime
        $this->assertThat($response->getStatusResult()->getLastModified(), $this->logicalOr(
            $this->isNull(),
            $this->isInstanceOf(\DateTime::class)
        ));
        $this->assertSame(self::$name, $response->getStatusResult()->getCoreName());

        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore('unknowncore');

        $coreAdminQuery->setAction($statusAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
        $this->assertSame(0, $response->getStatusResult()->getUptime());
        $this->assertNull($response->getStatusResult()->getLastModified());
        $this->assertNull($response->getStatusResult()->getStartTime());
    }

    public function testSplitAndMerge()
    {
        $coreAdminQuery = self::$client->createCoreAdmin();
        // create core *_a
        $createAction = $coreAdminQuery->createCreate();
        $createAction->setCore(self::$name.'_a');
        $createAction->setConfigSet('sample_techproducts_configs');
        $coreAdminQuery->setAction($createAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // create core *_b
        $createAction = $coreAdminQuery->createCreate();
        $createAction->setCore(self::$name.'_b');
        $createAction->setConfigSet('sample_techproducts_configs');
        $coreAdminQuery->setAction($createAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // split the original core into *_a and *_b
        $splitAction = $coreAdminQuery->createSplit();
        $splitAction->setCore(self::$name);
        $splitAction->setTargetCore([self::$name.'_a', self::$name.'_b']);
        $coreAdminQuery->setAction($splitAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // reload core *_a
        $reloadAction = $coreAdminQuery->createReload();
        $reloadAction->setCore(self::$name.'_a');
        $coreAdminQuery->setAction($reloadAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // reload core *_b
        $reloadAction = $coreAdminQuery->createReload();
        $reloadAction->setCore(self::$name.'_b');
        $coreAdminQuery->setAction($reloadAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // check that we have 32 documents in the original core, 16 in *_a and 16 in *_b
        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore(self::$name);
        $coreAdminQuery->setAction($statusAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
        $this->assertSame(32, $response->getStatusResult()->getNumberOfDocuments());

        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore(self::$name.'_a');
        $coreAdminQuery->setAction($statusAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
        $this->assertSame(16, $response->getStatusResult()->getNumberOfDocuments());

        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore(self::$name.'_b');
        $coreAdminQuery->setAction($statusAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
        $this->assertSame(16, $response->getStatusResult()->getNumberOfDocuments());
        // now we cleanup the created cores
        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore(self::$name.'_a');
        $unloadAction->setDeleteDataDir(true)->setDeleteIndex(true)->setDeleteInstanceDir(true);
        $coreAdminQuery->setAction($unloadAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore(self::$name.'_b');
        $unloadAction->setDeleteDataDir(true)->setDeleteIndex(true)->setDeleteInstanceDir(true);
        $coreAdminQuery->setAction($unloadAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
    }

    public function testGetStatusFromAllCoresWhenNoCoreNameWasSet()
    {
        $coreAdminQuery = self::$client->createCoreAdmin();

        // create new core using the techproducts config set
        $createAction = $coreAdminQuery->createCreate();
        $createAction->setCore(self::$name.'_new');
        $createAction->setConfigSet('sample_techproducts_configs');
        $coreAdminQuery->setAction($createAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // we now have two cores and when we retrieve the status for all we should have two status objects
        // (the core we created in setUpBeforeClass() and the one we created just now)
        $statusAction = $coreAdminQuery->createStatus();
        $coreAdminQuery->setAction($statusAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        $statusResults = $response->getStatusResults();
        $this->assertCount(2, $statusResults);
        $this->assertGreaterThan(0, $statusResults[0]->getUptime(), 'Can not get uptime of first core');

        // now we unload the created core again
        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore(self::$name.'_new');
        $unloadAction->setDeleteDataDir(true)->setDeleteIndex(true)->setDeleteInstanceDir(true);
        $coreAdminQuery->setAction($unloadAction);
        $response = self::$client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
    }
}
