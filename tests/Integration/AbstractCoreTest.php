<?php

namespace Solarium\Tests\Integration;

use Solarium\Core\Client\ClientInterface;
use Solarium\Exception\HttpException;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Add as AddStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Delete as DeleteStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Exists as ExistsStopwords;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Add as AddSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Delete as DeleteSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Exists as ExistsSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms;

abstract class AbstractCoreTest extends AbstractTechproductsTest
{
    /**
     * @var ClientInterface
     */
    protected $client;

    public function setUp()
    {
        $config = [
            'endpoint' => [
                'localhost' => [
                    'host' => '127.0.0.1',
                    'port' => 8983,
                    'path' => '/',
                    'core' => 'techproducts',
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
            $this->markTestSkipped('Solr techproducts example not reachable.');
        }
    }

    public function testCanReloadCore()
    {
        $coreAdminQuery = $this->client->createCoreAdmin();
        $reloadAction = $coreAdminQuery->createReload();
        $reloadAction->setCore('techproducts');
        $coreAdminQuery->setAction($reloadAction);

        $result = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($result->getWasSuccessful());

        // reloading an unexisting core should not be succesful
        $this->expectException(HttpException::class);
        $reloadAction2 = $coreAdminQuery->createReload();
        $reloadAction2->setCore('nonExistingCore');
        $coreAdminQuery->setAction($reloadAction2);
        $this->client->coreAdmin($coreAdminQuery);
    }

    public function testCoreAdminStatus()
    {
        $coreAdminQuery = $this->client->createCoreAdmin();
        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore('techproducts');

        $coreAdminQuery->setAction($statusAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $json = json_decode($response->getResponse()->getBody());
        $this->assertTrue($response->getWasSuccessful());
        $this->assertGreaterThanOrEqual(0, $json->responseHeader->QTime);
        $this->assertNotNull($response->getStatusResult()->getUptime());
        $this->assertGreaterThan(0, $response->getStatusResult()->getStartTime()->format('U'));
        $this->assertGreaterThan(0, $response->getStatusResult()->getLastModified()->format('U'));
        $this->assertSame('techproducts', $response->getStatusResult()->getCoreName());

        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore('unknowncore');

        $coreAdminQuery->setAction($statusAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
        $this->assertSame(0, $response->getStatusResult()->getUptime());
        $this->assertNull($response->getStatusResult()->getLastModified());
        $this->assertNull($response->getStatusResult()->getStartTime());
    }

    public function testSplitAndMerge()
    {
        $coreAdminQuery = $this->client->createCoreAdmin();
        // create core techproducts_a
        $createAction = $coreAdminQuery->createCreate();
        $createAction->setCore('techproducts_a');
        $createAction->setConfigSet('sample_techproducts_configs');
        $coreAdminQuery->setAction($createAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // create core techproducts_b
        $createAction = $coreAdminQuery->createCreate();
        $createAction->setCore('techproducts_b');
        $createAction->setConfigSet('sample_techproducts_configs');
        $coreAdminQuery->setAction($createAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // split the techproducts core into techproducts_a and techproducts_b
        $splitAction = $coreAdminQuery->createSplit();
        $splitAction->setCore('techproducts');
        $splitAction->setTargetCore(['techproducts_a', 'techproducts_b']);
        $coreAdminQuery->setAction($splitAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // reload core techproducts_a
        $reloadAction = $coreAdminQuery->createReload();
        $reloadAction->setCore('techproducts_a');
        $coreAdminQuery->setAction($reloadAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // reload core techproducts_b
        $reloadAction = $coreAdminQuery->createReload();
        $reloadAction->setCore('techproducts_b');
        $coreAdminQuery->setAction($reloadAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // check that we have 32 documents in techproducts 16 in techproducts_a and 16 in techproducts_b
        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore('techproducts');
        $coreAdminQuery->setAction($statusAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
        $this->assertSame(32, $response->getStatusResult()->getNumberOfDocuments());

        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore('techproducts_a');
        $coreAdminQuery->setAction($statusAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
        $this->assertSame(16, $response->getStatusResult()->getNumberOfDocuments());

        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore('techproducts_b');
        $coreAdminQuery->setAction($statusAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
        $this->assertSame(16, $response->getStatusResult()->getNumberOfDocuments());
        // now we cleanup the created cores
        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore('techproducts_a');
        $unloadAction->setDeleteDataDir(true)->setDeleteIndex(true)->setDeleteInstanceDir(true);
        $coreAdminQuery->setAction($unloadAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore('techproducts_b');
        $unloadAction->setDeleteDataDir(true)->setDeleteIndex(true)->setDeleteInstanceDir(true);
        $coreAdminQuery->setAction($unloadAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
    }

    public function testGetStatusFromAllCoreWhenNoCoreNameWasSet()
    {
        $coreAdminQuery = $this->client->createCoreAdmin();

        // create core techproducts_new
        $createAction = $coreAdminQuery->createCreate();
        $createAction->setCore('techproducts_new');
        $createAction->setConfigSet('sample_techproducts_configs');
        $coreAdminQuery->setAction($createAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // we now have two cores and when we retrieve the status for all we should have two status objects
        $statusAction = $coreAdminQuery->createStatus();
        $coreAdminQuery->setAction($statusAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        $statusResults = $response->getStatusResults();
        $this->assertSame(2, count($statusResults));
        $this->assertGreaterThan(0, $statusResults[0]->getUptime(), 'Can not get uptime of first core');

        // now we unload the created core again
        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore('techproducts_new');
        $unloadAction->setDeleteDataDir(true)->setDeleteIndex(true)->setDeleteInstanceDir(true);
        $coreAdminQuery->setAction($unloadAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
    }

    public function testManagedStopwords()
    {
        $query = $this->client->createManagedStopwords();
        $query->setName('english');
        $term = 'managed_stopword_test';

        // Add stopwords
        $add = new AddStopwords();
        $add->setStopwords([$term]);
        $query->setCommand($add);
        $result = $this->client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if single stopword exist
        $exists = new ExistsStopwords();
        $exists->setTerm($term);
        $query->setCommand($exists);
        $result = $this->client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // We need to remove the current command in order to have no command. Having no command lists the items.
        $query->removeCommand();

        // List stopwords
        $result = $this->client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());
        $items = $result->getItems();
        $this->assertContains($term, $items);

        // Delete stopword
        $delete = new DeleteStopwords();
        $delete->setTerm($term);
        $query->setCommand($delete);
        $result = $this->client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if stopword is gone
        $this->expectException(HttpException::class);
        $exists = new ExistsStopwords();
        $exists->setTerm($term);
        $query->setCommand($exists);
        $this->client->execute($query);
    }

    public function testManagedSynonyms()
    {
        $query = $this->client->createManagedSynonyms();
        $query->setName('english');
        $term = 'managed_synonyms_test';

        // Add synonyms
        $add = new AddSynonyms();
        $synonyms = new Synonyms();
        $synonyms->setTerm($term);
        $synonyms->setSynonyms(['managed_synonym', 'synonym_test']);
        $add->setSynonyms($synonyms);
        $query->setCommand($add);
        $result = $this->client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if single synonym exist
        $exists = new ExistsSynonyms();
        $exists->setTerm($term);
        $query->setCommand($exists);
        $result = $this->client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());
        $this->assertSame(['managed_synonyms_test' => ['managed_synonym', 'synonym_test']], $result->getData());

        // We need to remove the current command in order to have no command. Having no command lists the items.
        $query->removeCommand();

        // List synonyms
        $result = $this->client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());
        $items = $result->getItems();
        $success = false;
        foreach ($items as $item) {
            if ('managed_synonyms_test' === $item->getTerm()) {
                $success = true;
            }
        }
        if (!$success) {
            $this->fail('Couldn\'t find synonym.');
        }

        // Delete synonyms
        $delete = new DeleteSynonyms();
        $delete->setTerm($term);
        $query->setCommand($delete);
        $result = $this->client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if synonyms are gone
        $this->expectException(HttpException::class);
        $exists = new ExistsSynonyms();
        $exists->setTerm($term);
        $query->setCommand($exists);
        $this->client->execute($query);
    }

    public function testManagedResources()
    {
        $query = $this->client->createManagedResources();
        $result = $this->client->execute($query);
        $items = $result->getItems();
        $this->assertCount(2, $items);
    }
}
