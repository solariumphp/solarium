<?php

namespace Solarium\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\QueryTraits\TermsTrait;
use Solarium\Component\Result\Terms\Result;
use Solarium\Core\Client\ClientInterface;
use Solarium\Exception\HttpException;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Add as AddStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Delete as DeleteStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Exists as ExistsStopwords;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Add as AddSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Delete as DeleteSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Exists as ExistsSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\Document;

abstract class AbstractTechproductsTest extends TestCase
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
                    'path' => '/solr/',
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

    /**
     * The ping test succeeds if no exception is thrown.
     */
    public function testPing()
    {
        $ping = $this->client->createPing();
        $result = $this->client->ping($ping);
        $this->assertSame(0, $result->getStatus());
    }

    public function testSelect()
    {
        $select = $this->client->createSelect();
        $select->setSorts(['id' => SelectQuery::SORT_ASC]);
        $result = $this->client->select($select);
        $this->assertSame(32, $result->getNumFound());
        $this->assertSame(10, $result->count());

        $ids = [];
        /** @var \Solarium\QueryType\Select\Result\Document $document */
        foreach ($result as $document) {
            $ids[] = $document->id;
        }
        $this->assertEquals([
            '0579B002',
            '100-435805',
            '3007WFP',
            '6H500F0',
            '9885A004',
            'EN7800GTX/2DHTV/256M',
            'EUR',
            'F8V7067-APL-KIT',
            'GB18030TEST',
            'GBP',
            ], $ids);
    }

    public function testRangeQueries()
    {
        $select = $this->client->createSelect();

        $select->setQuery(
            $select->getHelper()->rangeQuery('price', null, 80)
        );
        $result = $this->client->select($select);
        $this->assertSame(6, $result->getNumFound());
        $this->assertSame(6, $result->count());

        // VS1GB400C3 costs 74.99 and is the only product in the range between 70.23 and 80.00.
        $select->setQuery(
            $select->getHelper()->rangeQuery('price', 70.23, 80)
        );
        $result = $this->client->select($select);
        $this->assertSame(1, $result->getNumFound());
        $this->assertSame(1, $result->count());

        $select->setQuery(
            $select->getHelper()->rangeQuery('price', 74.99, null)
        );
        $result = $this->client->select($select);
        $this->assertSame(11, $result->getNumFound());
        $this->assertSame(10, $result->count());

        $select->setQuery(
            $select->getHelper()->rangeQuery('price', 74.99, null, false)
        );
        $result = $this->client->select($select);
        $this->assertSame(10, $result->getNumFound());
        $this->assertSame(10, $result->count());
    }

    public function testFacetHighlightSpellcheckComponent()
    {
        $select = $this->client->createSelect();
        // In the techproducts example, the request handler "select" doesn't neither contain a spellcheck component nor
        // a highlighter or facets. But the "browse" request handler does.
        $select->setHandler('browse');
        // Search for misspelled "power cort".
        $select->setQuery('power cort');

        $spellcheck = $select->getSpellcheck();
        // Some spellcheck dictionaries needs to build first, but not on every request!
        $spellcheck->setBuild(true);

        $result = $this->client->select($select);
        $this->assertSame(0, $result->getNumFound());

        $this->assertSame([
            'power' => 'power',
            'cort' => 'cord',
        ],
        $result->getSpellcheck()->getCollations()[0]->getCorrections());

        $words = [];
        foreach ($result->getSpellcheck()->getSuggestions()[0]->getWords() as $suggestion) {
            $words[] = $suggestion['word'];
        }
        $this->assertEquals([
            'corp',
            'cord',
            'card',
        ], $words);

        $select->setQuery('power cord');
        // Activate highlighting.
        $select->getHighlighting();
        $facetSet = $select->getFacetSet();
        $facetSet->createFacetField('stock')->setField('inStock');

        $result = $this->client->select($select);
        $this->assertSame(1, $result->getNumFound());

        foreach ($result as $document) {
            $this->assertSame('F8V7067-APL-KIT', $document->id);
        }

        $this->assertSame(
            ['car <b>power</b> adapter, white'],
            $result->getHighlighting()->getResult('F8V7067-APL-KIT')->getField('features'));

        $this->assertSame(
            ['Belkin Mobile <b>Power</b> <b>Cord</b> for iPod w&#x2F; Dock'],
            $result->getHighlighting()->getResult('F8V7067-APL-KIT')->getField('name'));

        $this->assertSame([
                'features' => ['car <b>power</b> adapter, white'],
                'name' => ['Belkin Mobile <b>Power</b> <b>Cord</b> for iPod w&#x2F; Dock'],
            ],
            $result->getHighlighting()->getResult('F8V7067-APL-KIT')->getFields());

        foreach ($result->getFacetSet() as $facetFieldName => $facetField) {
            $this->assertSame('stock', $facetFieldName);
            // The power cord is not in stock! In the techproducts example that is reflected by the string 'false'.
            $this->assertSame(1, $facetField->getValues()['false']);
        }
    }

    public function testQueryElevation()
    {
        $select = $this->client->createSelect();
        // In the techproducts example, the request handler "select" doesn't contain a query elevation component.
        // But the "elevate" request handler does.
        $select->setHandler('elevate');
        $select->setQuery('electronics');
        $select->setSorts(['id' => SelectQuery::SORT_ASC]);

        $elevate = $select->getQueryElevation();
        $elevate->setForceElevation(true);
        $elevate->setElevateIds(['VS1GB400C3', 'VDBDB1A16']);
        $elevate->setExcludeIds(['SP2514N', '6H500F0']);

        $result = $this->client->select($select);
        // The techproducts example contains 14 'electronics', 2 of them are excluded.
        $this->assertSame(12, $result->getNumFound());
        // The first two results are elevated and ignore the sort order.
        $iterator = $result->getIterator();
        $document = $iterator->current();
        $this->assertSame('VS1GB400C3', $document->id);
        $this->assertTrue($document->{'[elevated]'});
        $iterator->next();
        $document = $iterator->current();
        $this->assertSame('VDBDB1A16', $document->id);
        $this->assertTrue($document->{'[elevated]'});
        // Further results aren't elevated.
        $iterator->next();
        $document = $iterator->current();
        $this->assertFalse($document->{'[elevated]'});
    }

    public function testSpatial()
    {
        $select = $this->client->createSelect();

        $select->setQuery(
            $select->getHelper()->geofilt('store', 40, -100, 100000)
        );
        $result = $this->client->select($select);
        $this->assertSame(14, $result->getNumFound());
        $this->assertSame(10, $result->count());

        $select->setQuery(
            $select->getHelper()->geofilt('store', 40, -100, 1000)
        );
        $result = $this->client->select($select);
        $this->assertSame(10, $result->getNumFound());
        $this->assertSame(10, $result->count());
    }

    public function testSpellcheck()
    {
        $spellcheck = $this->client->createSpellcheck();
        $spellcheck->setQuery('power cort');
        // Some spellcheck dictionaries needs to build first, but not on every request!
        $spellcheck->setBuild(true);
        $result = $this->client->spellcheck($spellcheck);
        $words = [];
        foreach ($result as $term => $suggestions) {
            $this->assertSame('cort', $term);
            foreach ($suggestions as $suggestion) {
                $words[] = $suggestion['word'];
            }
        }
        $this->assertEquals([
            'corp',
            'cord',
            'card',
            ], $words);
    }

    public function testSuggester()
    {
        $suggester = $this->client->createSuggester();
        // The techproducts example doesn't provide a default suggester, but 'mySuggester'.
        $suggester->setDictionary('mySuggester');
        $suggester->setQuery('electronics');
        // A suggester dictionary needs to build first, but not on every request!
        $suggester->setBuild(true);
        $result = $this->client->suggester($suggester);
        $phrases = [];
        foreach ($result as $dictionary => $terms) {
            $this->assertSame('mySuggester', $dictionary);
            foreach ($terms as $term => $suggestions) {
                $this->assertSame('electronics', $term);
                foreach ($suggestions as $suggestion) {
                    $phrases[] = $suggestion['term'];
                }
            }
        }
        $this->assertEquals([
            'electronics',
            'electronics and computer1',
            'electronics and stuff2',
            ], $phrases);
    }

    public function testTerms()
    {
        $terms = $this->client->createTerms();
        $terms->setFields('name');
        $result = $this->client->terms($terms);

        $this->assertEquals([
            'one' => 5,
            184 => 3,
            '1gb' => 3,
            3200 => 3,
            400 => 3,
            'ddr' => 3,
            'gb' => 3,
            'ipod' => 3,
            'memory' => 3,
            'pc' => 3,
        ], $result->getTerms('name'));
    }

    public function testTermsComponent()
    {
        $this->client->registerQueryType('test', '\Solarium\Tests\Integration\TestQuery');
        $select = $this->client->createQuery('test');
        $terms = $select->getTerms();
        $terms->setFields('name');
        $result = $this->client->select($select);
        /** @var Result $termsComponentResult */
        $termsComponentResult = $result->getComponent(ComponentAwareQueryInterface::COMPONENT_TERMS);

        $this->assertEquals([
            'one',
            184,
            '1gb',
            3200,
            400,
            'ddr',
            'gb',
            'ipod',
            'memory',
            'pc',
        ], $termsComponentResult->getField('name')->getTerms());

        $this->assertEquals([
            'one' => 5,
            184 => 3,
            '1gb' => 3,
            3200 => 3,
            400 => 3,
            'ddr' => 3,
            'gb' => 3,
            'ipod' => 3,
            'memory' => 3,
            'pc' => 3,
        ], $termsComponentResult->getAll()['name']);

        $terms = [];
        foreach ($termsComponentResult as $field) {
            foreach ($field as $term => $count) {
                $terms[$term] = $count;
            }
        }
        $this->assertEquals([
            'one' => 5,
            184 => 3,
            '1gb' => 3,
            3200 => 3,
            400 => 3,
            'ddr' => 3,
            'gb' => 3,
            'ipod' => 3,
            'memory' => 3,
            'pc' => 3,
        ], $terms);
    }

    public function testReRankQuery()
    {
        $select = $this->client->createSelect();
        $select->setQuery('inStock:true');
        $select->setRows(2);
        $result = $this->client->select($select);
        $this->assertSame(17, $result->getNumFound());
        $this->assertSame(2, $result->count());

        $ids = [];
        /** @var \Solarium\QueryType\Select\Result\Document $document */
        foreach ($result as $document) {
            $ids[] = $document->id;
        }

        $reRankQuery = $select->getReRankQuery();
        $reRankQuery->setQuery('popularity:10');
        $result = $this->client->select($select);
        $this->assertSame(17, $result->getNumFound());
        $this->assertSame(2, $result->count());

        $rerankedids = [];
        /** @var \Solarium\QueryType\Select\Result\Document $document */
        foreach ($result as $document) {
            $rerankedids[] = $document->id;
        }
        $this->assertNotSame($ids, $rerankedids);
        // These two ducuments have a popularity of 10 and should ranked highest.
        $this->assertArraySubset([
            'MA147LL/A',
            'SOLR1000',
        ], $rerankedids);
    }

    public function testPrefetchIterator()
    {
        $select = $this->client->createSelect();
        $prefetch = $this->client->getPlugin('prefetchiterator');
        $prefetch->setPrefetch(2);
        $prefetch->setQuery($select);

        // count() uses getNumFound() on the result set and wouldn't actually test if all results are iterated
        for ($i = 0; $prefetch->valid(); ++$i) {
            $prefetch->next();
        }

        $this->assertSame(32, $i);
    }

    public function testPrefetchIteratorWithCursormark()
    {
        $select = $this->client->createSelect();
        $select->setCursormark('*');
        $select->addSort('id', SelectQuery::SORT_ASC);
        $prefetch = $this->client->getPlugin('prefetchiterator');
        $prefetch->setPrefetch(2);
        $prefetch->setQuery($select);

        // count() uses getNumFound() on the result set and wouldn't actually test if all results are iterated
        for ($i = 0; $prefetch->valid(); ++$i) {
            $prefetch->next();
        }

        $this->assertSame(32, $i);
    }

    public function testPrefetchIteratorWithoutAndWithCursormark()
    {
        $select = $this->client->createSelect();
        $select->addSort('id', SelectQuery::SORT_ASC);
        $prefetch = $this->client->getPlugin('prefetchiterator');
        $prefetch->setPrefetch(2);
        $prefetch->setQuery($select);

        $without = [];
        foreach ($prefetch as $document) {
            $without = $document->id;
        }

        $select = $this->client->createSelect();
        $select->setCursormark('*');
        $select->addSort('id', SelectQuery::SORT_ASC);
        $prefetch->setQuery($select);

        $with = [];
        foreach ($prefetch as $document) {
            $with = $document->id;
        }

        $this->assertSame($without, $with);
    }

    public function testExtractIntoDocument()
    {
        $extract = $this->client->createExtract();
        $extract->setUprefix('attr_');
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testpdf.pdf');
        $extract->setCommit(true);
        $extract->setCommitWithin(0);
        $extract->setOmitHeader(false);

        // add document
        $doc = $extract->createDocument();
        $doc->id = 'extract-test';
        $extract->setDocument($doc);

        $this->client->extract($extract);

        // now get the document and check the content
        $select = $this->client->createSelect();
        $select->setQuery('id:extract-test');
        $selectResult = $this->client->select($select);
        $iterator = $selectResult->getIterator();

        /** @var Document $document */
        $document = $iterator->current();
        $this->assertSame('PDF Test', trim($document['content'][0]), 'Written document does not contain extracted result');

        // now cleanup the document the have the initial index state
        $update = $this->client->createUpdate();
        $update->addDeleteById('extract-test');
        $update->addCommit(true, true);
        $this->client->extract($update);
    }

    public function testExtractTextOnly()
    {
        $query = $this->client->createExtract();
        $fileName = 'testpdf.pdf';
        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.$fileName);
        $query->setExtractOnly(true);
        $query->addParam('extractFormat', 'text');

        $response = $this->client->extract($query);
        $json = json_decode($response->getResponse()->getBody());

        $content = $json->{$fileName};
        $this->assertSame('PDF Test', trim($content), 'Can not extract the plain content from the file');
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
            if($item->getTerm() === 'managed_synonyms_test')
            {
                $success = true;
            }
        }
        if(!$success)
        {
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

class TestQuery extends SelectQuery
{
    use TermsTrait;

    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->componentTypes[ComponentAwareQueryInterface::COMPONENT_TERMS] = 'Solarium\Component\Terms';
        // Unfortunately the terms request Handler is the only one containing a terms component.
        $this->setHandler('terms');
    }
}
