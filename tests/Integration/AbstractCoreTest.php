<?php

namespace Solarium\Tests\Integration;

use Solarium\Core\Client\ClientInterface;
use Solarium\Exception\HttpException;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Add as AddStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Config as ConfigStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Create as CreateStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Delete as DeleteStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Exists as ExistsStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Remove as RemoveStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\InitArgs as InitArgsStopwords;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Add as AddSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Config as ConfigSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Create as CreateSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Delete as DeleteSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Exists as ExistsSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Remove as RemoveSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\InitArgs as InitArgsSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms;

abstract class AbstractCoreTest extends AbstractTechproductsTest
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var string
     */
    private static $core;

    /**
     * @var array
     */
    private static $config;

    public static function setUpBeforeClass(): void
    {
        self::$core = uniqid();

        self::$config = [
            'endpoint' => [
                'localhost' => [
                    'host' => '127.0.0.1',
                    'port' => 8983,
                    'path' => '/',
                    'core' => self::$core,
                ],
            ],
        ];

        $client = TestClientFactory::createWithPsr18Adapter(self::$config);

        try {
            $coreAdminQuery = $client->createCoreAdmin();

            // create core with unique name using the techproducts configset
            $createAction = $coreAdminQuery->createCreate();
            $createAction->setCore(self::$core);
            $createAction->setConfigSet('sample_techproducts_configs');
            $coreAdminQuery->setAction($createAction);
            $response = $client->coreAdmin($coreAdminQuery);
            static::assertTrue($response->getWasSuccessful());

            // merge techproducts index into our new core
            $mergeAction = $coreAdminQuery->createMergeIndexes();
            $mergeAction->setCore(self::$core);
            $mergeAction->setSrcCore(['techproducts']);
            $coreAdminQuery->setAction($mergeAction);
            $response = $client->coreAdmin($coreAdminQuery);
            self::assertTrue($response->getWasSuccessful());

            $ping = $client->createPing();
            $client->ping($ping);
        } catch (\Exception $e) {
            static::markTestSkipped('Solr techproducts example not reachable.');
        }
    }

    public function setUp(): void
    {
        $this->client = TestClientFactory::createWithPsr18Adapter(self::$config);

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
        $reloadAction->setCore(self::$core);
        $coreAdminQuery->setAction($reloadAction);

        $result = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($result->getWasSuccessful());

        // reloading a non existing core should not be successful
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
        $statusAction->setCore(self::$core);

        $coreAdminQuery->setAction($statusAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
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
        $this->assertSame(self::$core, $response->getStatusResult()->getCoreName());

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
        // create core *_a
        $createAction = $coreAdminQuery->createCreate();
        $createAction->setCore(self::$core.'_a');
        $createAction->setConfigSet('sample_techproducts_configs');
        $coreAdminQuery->setAction($createAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // create core *_b
        $createAction = $coreAdminQuery->createCreate();
        $createAction->setCore(self::$core.'_b');
        $createAction->setConfigSet('sample_techproducts_configs');
        $coreAdminQuery->setAction($createAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // split the original core into *_a and *_b
        $splitAction = $coreAdminQuery->createSplit();
        $splitAction->setCore(self::$core);
        $splitAction->setTargetCore([self::$core.'_a', self::$core.'_b']);
        $coreAdminQuery->setAction($splitAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // reload core *_a
        $reloadAction = $coreAdminQuery->createReload();
        $reloadAction->setCore(self::$core.'_a');
        $coreAdminQuery->setAction($reloadAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // reload core *_b
        $reloadAction = $coreAdminQuery->createReload();
        $reloadAction->setCore(self::$core.'_b');
        $coreAdminQuery->setAction($reloadAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // check that we have 32 documents in the original core, 16 in *_a and 16 in *_b
        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore(self::$core);
        $coreAdminQuery->setAction($statusAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
        $this->assertSame(32, $response->getStatusResult()->getNumberOfDocuments());

        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore(self::$core.'_a');
        $coreAdminQuery->setAction($statusAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
        $this->assertSame(16, $response->getStatusResult()->getNumberOfDocuments());

        $statusAction = $coreAdminQuery->createStatus();
        $statusAction->setCore(self::$core.'_b');
        $coreAdminQuery->setAction($statusAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
        $this->assertSame(16, $response->getStatusResult()->getNumberOfDocuments());
        // now we cleanup the created cores
        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore(self::$core.'_a');
        $unloadAction->setDeleteDataDir(true)->setDeleteIndex(true)->setDeleteInstanceDir(true);
        $coreAdminQuery->setAction($unloadAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore(self::$core.'_b');
        $unloadAction->setDeleteDataDir(true)->setDeleteIndex(true)->setDeleteInstanceDir(true);
        $coreAdminQuery->setAction($unloadAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());
    }

    public function testGetStatusFromAllCoreWhenNoCoreNameWasSet()
    {
        $coreAdminQuery = $this->client->createCoreAdmin();

        // create new core using the techproducts config set
        $createAction = $coreAdminQuery->createCreate();
        $createAction->setCore(self::$core.'_new');
        $createAction->setConfigSet('sample_techproducts_configs');
        $coreAdminQuery->setAction($createAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        // we now have three cores and when we retrieve the status for all we should have three status objects
        // (the original techproducts, the core we created in setUpBeforeClass() and the one we created just now)
        $statusAction = $coreAdminQuery->createStatus();
        $coreAdminQuery->setAction($statusAction);
        $response = $this->client->coreAdmin($coreAdminQuery);
        $this->assertTrue($response->getWasSuccessful());

        $statusResults = $response->getStatusResults();
        $this->assertCount(3, $statusResults);
        $this->assertGreaterThan(0, $statusResults[0]->getUptime(), 'Can not get uptime of first core');

        // now we unload the created core again
        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore(self::$core.'_new');
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

        // Check if single stopword exists
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

    public function testManagedStopwordsCreation()
    {
        $query = $this->client->createManagedStopwords();
        $query->setName(uniqid());
        $term = 'managed_stopword_test';

        // Create a new stopword list
        $create = new CreateStopwords();
        $query->setCommand($create);
        $result = $this->client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Whatever happens next ...
        try {
            // Configure the new list to be case sensitive
            $initArgs = new InitArgsStopwords();
            $initArgs->setIgnoreCase(false);
            $config = new ConfigStopwords();
            $config->setInitArgs($initArgs);
            $query->setCommand($config);
            $result = $this->client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check the configuration
            $query->removeCommand();
            $result = $this->client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());
            $this->assertFalse($result->isIgnoreCase());

            // Check if we can add to it
            $add = new AddStopwords();
            $add->setStopwords([$term]);
            $query->setCommand($add);
            $result = $this->client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if stopword exists in its original lowercase form
            $exists = new ExistsStopwords();
            $exists->setTerm($term);
            $query->setCommand($exists);
            $result = $this->client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if stopword DOESN'T exist in uppercase form
            $this->expectException(HttpException::class);
            $exists->setTerm(strtoupper($term));
            $query->setCommand($exists);
            $this->client->execute($query);
        }
        // ... we have to remove the created resource!
        finally {
            // Remove the stopword list
            $remove = new RemoveStopwords();
            $query->setCommand($remove);
            $result = $this->client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if stopword list is gone
            $this->expectException(HttpException::class);
            $query->removeCommand();
            $this->client->execute($query);
        }
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

        // Check if single synonym exists
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

    public function testManagedSynonymsCreation()
    {
        $query = $this->client->createManagedSynonyms();
        $query->setName(uniqid());
        $term = 'managed_synonyms_test';

        // Create a new synonym map
        $create = new CreateSynonyms();
        $query->setCommand($create);
        $result = $this->client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Whatever happens next ...
        try {
            // Configure the new map to be case sensitive and use the 'solr' format
            $initArgs = new InitArgsSynonyms();
            $initArgs->setIgnoreCase(false);
            $initArgs->setFormat(InitArgsSynonyms::FORMAT_SOLR);
            $config = new ConfigSynonyms();
            $config->setInitArgs($initArgs);
            $query->setCommand($config);
            $result = $this->client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check the configuration
            $query->removeCommand();
            $result = $this->client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());
            $this->assertFalse($result->isIgnoreCase());
            $this->assertEquals(InitArgsSynonyms::FORMAT_SOLR, $result->getFormat());

            // Check if we can add to it
            $add = new AddSynonyms();
            $synonyms = new Synonyms();
            $synonyms->setTerm($term);
            $synonyms->setSynonyms(['managed_synonym', 'synonym_test']);
            $add->setSynonyms($synonyms);
            $query->setCommand($add);
            $result = $this->client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if synonym exists in its original lowercase form
            $exists = new ExistsSynonyms();
            $exists->setTerm($term);
            $query->setCommand($exists);
            $result = $this->client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());
            $this->assertSame(['managed_synonyms_test' => ['managed_synonym', 'synonym_test']], $result->getData());

            // Check if synonym DOESN'T exist in uppercase form
            $this->expectException(HttpException::class);
            $exists->setTerm(strtoupper($term));
            $query->setCommand($exists);
            $this->client->execute($query);
        }
        // ... we have to remove the created resource!
        finally {
            // Remove the synonym map
            $remove = new RemoveSynonyms();
            $query->setCommand($remove);
            $result = $this->client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if synonym map is gone
            $this->expectException(HttpException::class);
            $query->removeCommand();
            $this->client->execute($query);
        }
    }

    public function testManagedResources()
    {
        // Check if we can find the 2 default managed resources
        // (and account for additional resources we might have created while testing)
        $query = $this->client->createManagedResources();
        $result = $this->client->execute($query);
        $items = $result->getItems();
        $this->assertGreaterThanOrEqual(2, count($items));
    }

    public static function tearDownAfterClass(): void
    {
        $client = TestClientFactory::createWithPsr18Adapter(self::$config);

        $coreAdminQuery = $client->createCoreAdmin();

        // now we unload the core we created in setUpBeforeClass()
        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore(self::$core);
        $unloadAction->setDeleteDataDir(true)->setDeleteIndex(true)->setDeleteInstanceDir(true);
        $coreAdminQuery->setAction($unloadAction);
        $response = $client->coreAdmin($coreAdminQuery);
        static::assertTrue($response->getWasSuccessful());
    }
}
