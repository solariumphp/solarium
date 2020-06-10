<?php

namespace Solarium\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\QueryTraits\TermsTrait;
use Solarium\Component\Result\Terms\Result;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Request;
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
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\Document;

abstract class AbstractTechproductsTest extends TestCase
{
    /**
     * @var ClientInterface
     */
    protected static $client;

    /**
     * @var string
     */
    protected static $name;

    /**
     * @var array
     */
    protected static $config;

    /**
     * @var int
     */
    protected static $solrVersion;

    abstract protected static function createTechproducts(): void;

    public static function setUpBeforeClass(): void
    {
        self::$name = uniqid();

        static::createTechproducts();

        $ping = self::$client->createPing();
        self::$client->ping($ping);

        $query = self::$client->createApi([
            'version' => Request::API_V1,
            'handler' => 'admin/info/system',
        ]);
        $response = self::$client->execute($query);
        self::$solrVersion = $response->getData()['lucene']['solr-spec-version'];

        // disable automatic commits for update tests
        $query = self::$client->createApi([
            'version' => Request::API_V1,
            'handler' => self::$name.'/config',
            'method' => Request::METHOD_POST,
            'rawdata' => json_encode([
                'set-property' => [
                    'updateHandler.autoCommit.maxDocs' => -1,
                    'updateHandler.autoCommit.maxTime' => -1,
                    'updateHandler.autoCommit.openSearcher' => true,
                    'updateHandler.autoSoftCommit.maxDocs' => -1,
                    'updateHandler.autoSoftCommit.maxTime' => -1,
                ],
            ]),
        ]);
        self::$client->execute($query);

        // ensure correct config for update tests
        $query = self::$client->createApi([
            'version' => Request::API_V1,
            'handler' => self::$name.'/config/updateHandler',
        ]);
        $response = self::$client->execute($query);
        $config = $response->getData()['config'];
        static::assertEquals([
            'maxDocs' => -1,
            'maxTime' => -1,
            'openSearcher' => true,
        ], $config['updateHandler']['autoCommit']);
        static::assertEquals([
            'maxDocs' => -1,
            'maxTime' => -1,
        ], $config['updateHandler']['autoSoftCommit']);

        try {
            // index techproducts sample data
            foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'techproducts'.DIRECTORY_SEPARATOR.'*.xml') as $file) {
                $update = self::$client->createUpdate();

                if (null !== $encoding = self::getXmlEncoding($file)) {
                    $update->setInputEncoding($encoding);
                }

                $update->addRawXmlFile($file);
                self::$client->update($update);
            }

            $update = self::$client->createUpdate();
            $update->addCommit(true, true);
            self::$client->update($update);

            // check that everything was indexed properly
            $select = self::$client->createSelect();
            $select->setFields('id');
            $result = self::$client->select($select);
            static::assertSame(32, $result->getNumFound());

            $select->setQuery('êâîôû');
            $result = self::$client->select($select);
            static::assertCount(1, $result);
            static::assertSame([
                'id' => 'UTF8TEST',
            ], $result->getIterator()->current()->getFields());

            $select->setQuery('这是一个功能');
            $result = self::$client->select($select);
            static::assertCount(1, $result);
            static::assertSame([
                'id' => 'GB18030TEST',
            ], $result->getIterator()->current()->getFields());
        } catch (\Exception $e) {
            self::tearDownAfterClass();
            static::markTestSkipped('Solr techproducts sample data not indexed properly.');
        }
    }

    /**
     * The ping test succeeds if no exception is thrown.
     */
    public function testPing()
    {
        $ping = self::$client->createPing();
        $result = self::$client->ping($ping);
        $this->assertSame(0, $result->getStatus());
    }

    public function testSelect()
    {
        $select = self::$client->createSelect();
        $select->setSorts(['id' => SelectQuery::SORT_ASC]);
        $result = self::$client->select($select);
        $this->assertSame(32, $result->getNumFound());
        $this->assertCount(10, $result);

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
        $select = self::$client->createSelect();

        $select->setQuery(
            $select->getHelper()->rangeQuery('price', null, 80)
        );
        $result = self::$client->select($select);
        $this->assertSame(6, $result->getNumFound());
        $this->assertCount(6, $result);

        // VS1GB400C3 costs 74.99 and is the only product in the range between 70.23 and 80.00.
        $select->setQuery(
            $select->getHelper()->rangeQuery('price', 70.23, 80)
        );
        $result = self::$client->select($select);
        $this->assertSame(1, $result->getNumFound());
        $this->assertCount(1, $result);

        $select->setQuery(
            $select->getHelper()->rangeQuery('price', 74.99, null)
        );
        $result = self::$client->select($select);
        $this->assertSame(11, $result->getNumFound());
        $this->assertCount(10, $result);

        $select->setQuery(
            $select->getHelper()->rangeQuery('price', 74.99, null, false)
        );
        $result = self::$client->select($select);
        $this->assertSame(10, $result->getNumFound());
        $this->assertCount(10, $result);

        $select->setQuery(
            $select->getHelper()->rangeQuery('store', '-90,-90', '90,90', true, false)
        );
        $result = self::$client->select($select);
        $this->assertSame(2, $result->getNumFound());
        $this->assertCount(2, $result);

        $select->setQuery(
            $select->getHelper()->rangeQuery('store', '-90,-180', '90,180', true, false)
        );
        $result = self::$client->select($select);
        $this->assertSame(14, $result->getNumFound());
        $this->assertCount(10, $result);
    }

    /**
     * @todo this test should pass on Solr Cloud!
     *
     * @group skip_for_solr_cloud
     */
    public function testFacetHighlightSpellcheckComponent()
    {
        $select = self::$client->createSelect();
        // In the techproducts example, the request handler "select" doesn't neither contain a spellcheck component nor
        // a highlighter or facets. But the "browse" request handler does.
        $select->setHandler('browse');
        // Search for misspelled "power cort".
        $select->setQuery('power cort');

        $spellcheck = $select->getSpellcheck();
        // Some spellcheck dictionaries needs to build first, but not on every request!
        $spellcheck->setBuild(true);

        $result = self::$client->select($select);
        $this->assertSame(0, $result->getNumFound());

        $this->assertSame(
            [
                'power' => 'power',
                'cort' => 'cord',
            ],
            $result->getSpellcheck()->getCollations()[0]->getCorrections()
        );

        $words = [];
        foreach ($result->getSpellcheck()->getSuggestions()[0]->getWords() as $suggestion) {
            $words[] = $suggestion['word'];
        }
        $this->assertEquals([
            'corp',
            'cord',
            'card',
        ], $words);

        $spellcheck->setDictionary(['default', 'wordbreak']);

        $result = self::$client->select($select);
        $this->assertSame(0, $result->getNumFound());

        $this->assertSame(
            [
                'power' => 'power',
                'cort' => 'cord',
            ],
            $result->getSpellcheck()->getCollations()[0]->getCorrections()
        );

        $select->setQuery('power cord');
        // Activate highlighting.
        $select->getHighlighting();
        $facetSet = $select->getFacetSet();
        $facetSet->createFacetField('stock')->setField('inStock');

        $result = self::$client->select($select);
        $this->assertSame(1, $result->getNumFound());

        foreach ($result as $document) {
            $this->assertSame('F8V7067-APL-KIT', $document->id);
        }

        $this->assertSame(
            ['car <b>power</b> adapter, white'],
            $result->getHighlighting()->getResult('F8V7067-APL-KIT')->getField('features')
        );

        $this->assertSame(
            ['Belkin Mobile <b>Power</b> <b>Cord</b> for iPod w&#x2F; Dock'],
            $result->getHighlighting()->getResult('F8V7067-APL-KIT')->getField('name')
        );

        $this->assertSame(
            [
                'features' => ['car <b>power</b> adapter, white'],
                'name' => ['Belkin Mobile <b>Power</b> <b>Cord</b> for iPod w&#x2F; Dock'],
            ],
            $result->getHighlighting()->getResult('F8V7067-APL-KIT')->getFields()
        );

        foreach ($result->getFacetSet() as $facetFieldName => $facetField) {
            $this->assertSame('stock', $facetFieldName);
            // The power cord is not in stock! In the techproducts example that is reflected by the string 'false'.
            $this->assertSame(1, $facetField->getValues()['false']);
        }
    }

    public function testQueryElevation()
    {
        $select = self::$client->createSelect();
        // In the techproducts example, the request handler "select" doesn't contain a query elevation component.
        // But the "elevate" request handler does.
        $select->setHandler('elevate');
        $select->setQuery('electronics');
        $select->setSorts(['id' => SelectQuery::SORT_ASC]);

        $elevate = $select->getQueryElevation();
        $elevate->setForceElevation(true);
        $elevate->setElevateIds(['VS1GB400C3', 'VDBDB1A16']);
        $elevate->setExcludeIds(['SP2514N', '6H500F0']);

        $result = self::$client->select($select);
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
        $select = self::$client->createSelect();

        $select->setQuery(
            $select->getHelper()->geofilt('store', 40, -100, 100000)
        );
        $result = self::$client->select($select);
        $this->assertSame(14, $result->getNumFound());
        $this->assertCount(10, $result);

        $select->setQuery(
            $select->getHelper()->geofilt('store', 40, -100, 1000)
        );
        $result = self::$client->select($select);
        $this->assertSame(10, $result->getNumFound());
        $this->assertCount(10, $result);
    }

    public function testSpellcheck()
    {
        $spellcheck = self::$client->createSpellcheck();
        $spellcheck->setQuery('power cort');
        // Some spellcheck dictionaries needs to build first, but not on every request!
        $spellcheck->setBuild(true);
        $result = self::$client->spellcheck($spellcheck);
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
        $suggester = self::$client->createSuggester();
        // The techproducts example doesn't provide a default suggester, but 'mySuggester'.
        $suggester->setDictionary('mySuggester');
        $suggester->setQuery('electronics');
        // A suggester dictionary needs to build first, but not on every request!
        $suggester->setBuild(true);
        $result = self::$client->suggester($suggester);
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
        $this->assertContains('electronics', $phrases);
        $this->assertContains('electronics and computer1', $phrases);
        $this->assertContains('electronics and stuff2', $phrases);
    }

    public function testTerms()
    {
        $terms = self::$client->createTerms();
        $terms->setFields('name');

        // Setting distrib to true in a non cloud setup causes exceptions.
        if ($this instanceof AbstractCloudTest) {
            $terms->setDistrib(true);
        }

        $result = self::$client->terms($terms);

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
        self::$client->registerQueryType('test', '\Solarium\Tests\Integration\TestQuery');
        $select = self::$client->createQuery('test');

        // Setting distrib to true in a non cloud setup causes exceptions.
        if ($this instanceof AbstractCloudTest) {
            $select->setDistrib(true);
        }

        $terms = $select->getTerms();
        $terms->setFields('name');
        $result = self::$client->select($select);
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

    public function testUpdate()
    {
        $select = self::$client->createSelect();
        $select->setQuery('cat:solarium-test');
        $select->addSort('id', $select::SORT_ASC);
        $select->setFields('id,name,price');

        // add, but don't commit
        $update = self::$client->createUpdate();
        $doc1 = $update->createDocument();
        $doc1->setField('id', 'solarium-test-1');
        $doc1->setField('name', 'Solarium Test 1');
        $doc1->setField('cat', 'solarium-test');
        $doc1->setField('price', 3.14);
        $doc2 = $update->createDocument();
        $doc2->setField('id', 'solarium-test-2');
        $doc2->setField('name', 'Solarium Test 2');
        $doc2->setField('cat', 'solarium-test');
        $doc2->setField('price', 42.0);
        $update->addDocuments([$doc1, $doc2]);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);

        // commit
        $update = self::$client->createUpdate();
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(2, $result);
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-test-1',
            'name' => 'Solarium Test 1',
            'price' => 3.14,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-test-2',
            'name' => 'Solarium Test 2',
            'price' => 42.0,
        ], $iterator->current()->getFields());

        // delete by id and commit
        $update = self::$client->createUpdate();
        $update->addDeleteById('solarium-test-1');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test-2',
            'name' => 'Solarium Test 2',
            'price' => 42.0,
        ], $result->getIterator()->current()->getFields());

        // delete by query and commit
        $update = self::$client->createUpdate();
        $update->addDeleteQuery('cat:solarium-test');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);

        // optimize
        $update = self::$client->createUpdate();
        $update->addOptimize(true, false);
        $response = self::$client->update($update);
        $this->assertSame(0, $response->getStatus());

        // rollback is currently not supported in SolrCloud mode (SOLR-4895)
        if ($this instanceof AbstractServerTest) {
            // add, rollback, commit
            $update = self::$client->createUpdate();
            $doc1 = $update->createDocument();
            $doc1->setField('id', 'solarium-test-1');
            $doc1->setField('name', 'Solarium Test 1');
            $doc1->setField('cat', 'solarium-test');
            $doc1->setField('price', 3.14);
            $update->addDocument($doc1);
            self::$client->update($update);
            $update = self::$client->createUpdate();
            $update->addRollback();
            $update->addCommit(true, true);
            self::$client->update($update);
            $result = self::$client->select($select);
            $this->assertCount(0, $result);
        }

        // raw add and raw commit
        $update = self::$client->createUpdate();
        $update->addRawXmlCommand('<add><doc><field name="id">solarium-test-1</field><field name="name">Solarium Test 1</field><field name="cat">solarium-test</field><field name="price">3.14</field></doc></add>');
        $update->addRawXmlCommand('<commit softCommit="true" waitSearcher="true"/>');
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test-1',
            'name' => 'Solarium Test 1',
            'price' => 3.14,
        ], $result->getIterator()->current()->getFields());

        // grouped mixed raw commands
        $update = self::$client->createUpdate();
        $update->addRawXmlCommand('<update><add><doc><field name="id">solarium-test-2</field><field name="name">Solarium Test 2</field><field name="cat">solarium-test</field><field name="price">42</field></doc></add></update>');
        $update->addRawXmlCommand('<update><delete><id>solarium-test-1</id></delete><commit softCommit="true" waitSearcher="true"/></update>');
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test-2',
            'name' => 'Solarium Test 2',
            'price' => 42.0,
        ], $result->getIterator()->current()->getFields());

        // raw delete and regular commit
        $update = self::$client->createUpdate();
        $update->addRawXmlCommand('<delete><query>cat:solarium-test</query></delete>');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);

        // add from UTF-8 encoded files without and with Byte Order Mark and XML declaration
        $update = self::$client->createUpdate();
        foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testxml[1234]-add*.xml') as $file) {
            $update->addRawXmlFile($file);
        }
        $update->addCommit(true, true);
        self::$client->update($update);

        // add from non-UTF-8 encoded file
        $update = self::$client->createUpdate();
        $update->setInputEncoding('ISO-8859-1');
        $update->addRawXmlFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testxml5-add-iso-8859-1.xml');
        $update->addCommit(true, true);
        self::$client->update($update);

        $result = self::$client->select($select);
        $this->assertCount(5, $result);
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-test-1',
            'name' => 'Solarium Test 1',
            'price' => 3.14,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-test-2',
            'name' => 'Solarium Test 2',
            'price' => 42.0,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-test-3',
            'name' => 'Solarium Test 3',
            'price' => 17.01,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-test-4',
            'name' => 'Solarium Test 4',
            'price' => 3.59,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-test-5',
            'name' => 'Sølåríùm Tëst 5',
            'price' => 9.81,
        ], $iterator->current()->getFields());

        // delete from file with grouped delete commands
        $update = self::$client->createUpdate();
        $update->addRawXmlFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testxml6-delete.xml');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);
    }

    public function testModifiers()
    {
        $select = self::$client->createSelect();
        $select->setQuery('id:solarium-test');
        $select->addSort('id', $select::SORT_ASC);
        $select->setFields('id,name,cat,price');
        $update = self::$client->createUpdate();

        $doc = $update->createDocument();
        $doc->setField('id', 'solarium-test');
        $doc->setField('name', 'Solarium Test');
        $doc->setField('cat', 'solarium-test');
        $doc->setField('price', 17.01);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'cat' => [
                'solarium-test',
            ],
            'price' => 17.01,
        ], $result->getIterator()->current()->getFields());

        // set
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', 'modifier-set');
        $doc->setFieldModifier('cat', $doc::MODIFIER_SET);
        $doc->setField('price', 42.0);
        $doc->setFieldModifier('price', $doc::MODIFIER_SET);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'cat' => [
                'modifier-set',
            ],
            'price' => 42.0,
        ], $result->getIterator()->current()->getFields());

        // add & inc
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', 'modifier-add');
        $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
        $doc->setField('price', 5);
        $doc->setFieldModifier('price', $doc::MODIFIER_INC);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'cat' => [
                'modifier-set',
                'modifier-add',
            ],
            'price' => 47.0,
        ], $result->getIterator()->current()->getFields());

        // add multiple values (non-distinct)
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', ['modifier-add', 'modifier-add-another']);
        $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'cat' => [
                'modifier-set',
                'modifier-add',
                'modifier-add',
                'modifier-add-another',
            ],
            'price' => 47.0,
        ], $result->getIterator()->current()->getFields());

        // add-distinct
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', 'modifier-add');
        $doc->setFieldModifier('cat', $doc::MODIFIER_ADD_DISTINCT);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'cat' => [
                'modifier-set',
                'modifier-add',
                'modifier-add',
                'modifier-add-another',
            ],
            'price' => 47.0,
        ], $result->getIterator()->current()->getFields());

        // add-distinct with multiple values can add duplicates in Solr 7 cloud mode (SOLR-14550)
        if ('7' === strstr(self::$solrVersion, '.', true) && $this instanceof AbstractCloudTest) {
            // we still have to emulate a successful atomic update for the remainder of this test to pass
            $doc = $update->createDocument();
            $doc->setKey('id', 'solarium-test');
            $doc->setField('cat', 'modifier-add-distinct');
            $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
            $update->addDocument($doc);
            $update->addCommit(true, true);
            self::$client->update($update);
        }
        else {
            // add-distinct multiple values
            $doc = $update->createDocument();
            $doc->setKey('id', 'solarium-test');
            $doc->setField('cat', ['modifier-add', 'modifier-add-another', 'modifier-add-distinct']);
            $doc->setFieldModifier('cat', $doc::MODIFIER_ADD_DISTINCT);
            $update->addDocument($doc);
            $update->addCommit(true, true);
            self::$client->update($update);
        }
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'cat' => [
                'modifier-set',
                'modifier-add',
                'modifier-add',
                'modifier-add-another',
                'modifier-add-distinct',
            ],
            'price' => 47.0,
        ], $result->getIterator()->current()->getFields());

        // remove & negative inc
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', 'modifier-set');
        $doc->setFieldModifier('cat', $doc::MODIFIER_REMOVE);
        $doc->setField('price', -5);
        $doc->setFieldModifier('price', $doc::MODIFIER_INC);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'cat' => [
                'modifier-add',
                'modifier-add',
                'modifier-add-another',
                'modifier-add-distinct',
            ],
            'price' => 42.0,
        ], $result->getIterator()->current()->getFields());

        // remove multiple values
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', ['modifier-add', 'modifier-add-another']);
        $doc->setFieldModifier('cat', $doc::MODIFIER_REMOVE);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'cat' => [
                'modifier-add',
                'modifier-add-distinct',
            ],
            'price' => 42.0,
        ], $result->getIterator()->current()->getFields());

        // removeregex
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', '^.+-add$');
        $doc->setFieldModifier('cat', $doc::MODIFIER_REMOVEREGEX);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'cat' => [
                'modifier-add-distinct',
            ],
            'price' => 42.0,
        ], $result->getIterator()->current()->getFields());

        // set to empty list
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', []);
        $doc->setFieldModifier('cat', $doc::MODIFIER_SET);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'price' => 42.0,
        ], $result->getIterator()->current()->getFields());

        // add to missing field
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', ['solarium-test']);
        $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        // cat comes after price now because it was added later!
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'price' => 42.0,
            'cat' => [
                'solarium-test',
            ],
        ], $result->getIterator()->current()->getFields());

        // set to null
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', [null]);
        $doc->setFieldModifier('cat', $doc::MODIFIER_SET);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'price' => 42.0,
        ], $result->getIterator()->current()->getFields());

        // cleanup
        $update = self::$client->createUpdate();
        $update->addDeleteById('solarium-test');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);
    }

    public function testReRankQuery()
    {
        $select = self::$client->createSelect();
        $select->setQuery('inStock:true');
        $select->setRows(2);
        $result = self::$client->select($select);
        $this->assertSame(17, $result->getNumFound());
        $this->assertCount(2, $result);

        $ids = [];
        /** @var \Solarium\QueryType\Select\Result\Document $document */
        foreach ($result as $document) {
            $ids[] = $document->id;
        }

        $reRankQuery = $select->getReRankQuery();
        $reRankQuery->setQuery('popularity:10');
        $result = self::$client->select($select);
        $this->assertSame(17, $result->getNumFound());
        $this->assertCount(2, $result);

        $rerankedids = [];
        /** @var \Solarium\QueryType\Select\Result\Document $document */
        foreach ($result as $document) {
            $rerankedids[] = $document->id;
        }
        $this->assertNotSame($ids, $rerankedids);
        // These two ducuments have a popularity of 10 and should ranked highest.
        $this->assertSame([
            'MA147LL/A',
            'SOLR1000',
        ], $rerankedids);
    }

    public function testPrefetchIterator()
    {
        $select = self::$client->createSelect();
        $prefetch = self::$client->getPlugin('prefetchiterator');
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
        $select = self::$client->createSelect();
        $select->setCursormark('*');
        $select->addSort('id', SelectQuery::SORT_ASC);
        $prefetch = self::$client->getPlugin('prefetchiterator');
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
        $select = self::$client->createSelect();
        $select->addSort('id', SelectQuery::SORT_ASC);
        $prefetch = self::$client->getPlugin('prefetchiterator');
        $prefetch->setPrefetch(2);
        $prefetch->setQuery($select);

        $without = [];
        foreach ($prefetch as $document) {
            $without = $document->id;
        }

        $select = self::$client->createSelect();
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
        $extract = self::$client->createExtract();
        $extract->setUprefix('attr_');
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testpdf.pdf');
        $extract->setCommit(true);
        $extract->setCommitWithin(0);
        $extract->setOmitHeader(false);

        // add document
        $doc = $extract->createDocument();
        $doc->id = 'extract-test';
        $extract->setDocument($doc);

        self::$client->extract($extract);

        // now get the document and check the content
        $select = self::$client->createSelect();
        $select->setQuery('id:extract-test');
        $selectResult = self::$client->select($select);
        $iterator = $selectResult->getIterator();

        /** @var Document $document */
        $document = $iterator->current();
        $this->assertSame('PDF Test', trim($document['content'][0]), 'Written document does not contain extracted result');

        // now cleanup the document the have the initial index state
        $update = self::$client->createUpdate();
        $update->addDeleteById('extract-test');
        $update->addCommit(true, true);
        self::$client->update($update);
    }

    public function testExtractTextOnly()
    {
        $query = self::$client->createExtract();
        $fileName = 'testpdf.pdf';
        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.$fileName);
        $query->setExtractOnly(true);
        $query->addParam('extractFormat', 'text');

        $response = self::$client->extract($query);
        $this->assertSame('PDF Test', trim($response->getData()['testpdf.pdf']), 'Can not extract the plain content from the file');
    }

    public function testV2Api()
    {
        if (version_compare(self::$solrVersion, '7', '>=')) {
            $query = self::$client->createApi([
                'version' => Request::API_V2,
                'handler' => 'node/system',
            ]);
            $response = self::$client->execute($query);
            $this->assertArrayHasKey('lucene', $response->getData());
            $this->assertArrayHasKey('jvm', $response->getData());
            $this->assertArrayHasKey('system', $response->getData());

            $query = self::$client->createApi([
                'version' => Request::API_V2,
                'handler' => 'node/properties',
            ]);
            $response = self::$client->execute($query);
            $this->assertArrayHasKey('system.properties', $response->getData());

            $query = self::$client->createApi([
                'version' => Request::API_V2,
                'handler' => 'node/logging',
            ]);
            $response = self::$client->execute($query);
            $this->assertArrayHasKey('levels', $response->getData());
            $this->assertArrayHasKey('loggers', $response->getData());
        } else {
            $this->markTestSkipped('V2 API requires Solr 7.');
        }
    }

    public function testInputEncoding()
    {
        $select = self::$client->createSelect();
        $select->addSort('id', $select::SORT_ASC);
        $select->setFields('id,name,price');

        // input encoding: UTF-8 (default)
        $update = self::$client->createUpdate();
        $doc = $update->createDocument();
        $doc->setField('id', 'solarium-test-1');
        $doc->setField('name', 'Sølåríùm Tëst 1');
        $doc->setField('cat', ['solarium-test', 'áéíóú']);
        $doc->setField('price', 3.14);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);

        // input encoding: UTF-8 (default)
        // output encoding: UTF-8 (always)
        $select->setQuery('cat:áéíóú');
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test-1',
            'name' => 'Sølåríùm Tëst 1',
            'price' => 3.14,
        ], $result->getIterator()->current()->getFields());

        // input encoding: ISO-8859-1
        // output encoding: UTF-8 (always)
        $select->setQuery('cat:'.utf8_decode('áéíóú'));
        $select->setInputEncoding('ISO-8859-1');
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 'solarium-test-1',
            'name' => 'Sølåríùm Tëst 1',
            'price' => 3.14,
        ], $result->getIterator()->current()->getFields());

        // input encoding: ISO-8859-1
        $update = self::$client->createUpdate();
        $update->setInputEncoding('ISO-8859-1');
        $doc = $update->createDocument();
        $doc->setField('id', utf8_decode('solarium-test-2'));
        $doc->setField('name', utf8_decode('Sølåríùm Tëst 2'));
        $doc->setField('cat', [utf8_decode('solarium-test'), utf8_decode('áéíóú')]);
        $doc->setField('price', 42.0);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);

        // input encoding: UTF-8 (explicit)
        // output encoding: UTF-8 (always)
        $select->setQuery('cat:áéíóú');
        $select->setInputEncoding('UTF-8');
        $result = self::$client->select($select);
        $this->assertCount(2, $result);
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-test-1',
            'name' => 'Sølåríùm Tëst 1',
            'price' => 3.14,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-test-2',
            'name' => 'Sølåríùm Tëst 2',
            'price' => 42.0,
        ], $iterator->current()->getFields());

        $update = self::$client->createUpdate();
        $update->addDeleteQuery('cat:solarium-test');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);
    }

    public function testManagedStopwords()
    {
        $query = self::$client->createManagedStopwords();
        $query->setName('english');
        $term = 'managed_stopword_test';

        // Add stopwords
        $add = new AddStopwords();
        $add->setStopwords([$term]);
        $query->setCommand($add);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if single stopword exists
        $exists = new ExistsStopwords();
        $exists->setTerm($term);
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // We need to remove the current command in order to have no command. Having no command lists the items.
        $query->removeCommand();

        // List stopwords
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());
        $items = $result->getItems();
        $this->assertContains($term, $items);

        // Delete stopword
        $delete = new DeleteStopwords();
        $delete->setTerm($term);
        $query->setCommand($delete);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if stopword is gone
        $this->expectException(HttpException::class);
        $exists = new ExistsStopwords();
        $exists->setTerm($term);
        $query->setCommand($exists);
        self::$client->execute($query);
    }

    public function testManagedStopwordsCreation()
    {
        $query = self::$client->createManagedStopwords();
        $query->setName(uniqid());
        $term = 'managed_stopword_test';

        // Create a new stopword list
        $create = new CreateStopwords();
        $query->setCommand($create);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Whatever happens next ...
        try {
            // Configure the new list to be case sensitive
            $initArgs = new InitArgsStopwords();
            $initArgs->setIgnoreCase(false);
            $config = new ConfigStopwords();
            $config->setInitArgs($initArgs);
            $query->setCommand($config);
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check the configuration
            $query->removeCommand();
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());
            $this->assertFalse($result->isIgnoreCase());

            // Check if we can add to it
            $add = new AddStopwords();
            $add->setStopwords([$term]);
            $query->setCommand($add);
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if stopword exists in its original lowercase form
            $exists = new ExistsStopwords();
            $exists->setTerm($term);
            $query->setCommand($exists);
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if stopword DOESN'T exist in uppercase form
            $this->expectException(HttpException::class);
            $exists->setTerm(strtoupper($term));
            $query->setCommand($exists);
            self::$client->execute($query);
        }
        // ... we have to remove the created resource!
        finally {
            // Remove the stopword list
            $remove = new RemoveStopwords();
            $query->setCommand($remove);
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if stopword list is gone
            $this->expectException(HttpException::class);
            $query->removeCommand();
            self::$client->execute($query);
        }
    }

    public function testManagedSynonyms()
    {
        $query = self::$client->createManagedSynonyms();
        $query->setName('english');
        $term = 'managed_synonyms_test';

        // Add synonyms
        $add = new AddSynonyms();
        $synonyms = new Synonyms();
        $synonyms->setTerm($term);
        $synonyms->setSynonyms(['managed_synonym', 'synonym_test']);
        $add->setSynonyms($synonyms);
        $query->setCommand($add);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if single synonym exists
        $exists = new ExistsSynonyms();
        $exists->setTerm($term);
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());
        $this->assertSame(['managed_synonyms_test' => ['managed_synonym', 'synonym_test']], $result->getData());

        // We need to remove the current command in order to have no command. Having no command lists the items.
        $query->removeCommand();

        // List synonyms
        $result = self::$client->execute($query);
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
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if synonyms are gone
        $this->expectException(HttpException::class);
        $exists = new ExistsSynonyms();
        $exists->setTerm($term);
        $query->setCommand($exists);
        self::$client->execute($query);
    }

    public function testManagedSynonymsCreation()
    {
        $query = self::$client->createManagedSynonyms();
        $query->setName(uniqid());
        $term = 'managed_synonyms_test';

        // Create a new synonym map
        $create = new CreateSynonyms();
        $query->setCommand($create);
        $result = self::$client->execute($query);
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
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check the configuration
            $query->removeCommand();
            $result = self::$client->execute($query);
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
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if synonym exists in its original lowercase form
            $exists = new ExistsSynonyms();
            $exists->setTerm($term);
            $query->setCommand($exists);
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());
            $this->assertSame(['managed_synonyms_test' => ['managed_synonym', 'synonym_test']], $result->getData());

            // Check if synonym DOESN'T exist in uppercase form
            $this->expectException(HttpException::class);
            $exists->setTerm(strtoupper($term));
            $query->setCommand($exists);
            self::$client->execute($query);
        }
        // ... we have to remove the created resource!
        finally {
            // Remove the synonym map
            $remove = new RemoveSynonyms();
            $query->setCommand($remove);
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if synonym map is gone
            $this->expectException(HttpException::class);
            $query->removeCommand();
            self::$client->execute($query);
        }
    }

    public function testManagedResources()
    {
        // Check if we can find the 2 default managed resources
        // (and account for additional resources we might have created while testing)
        $query = self::$client->createManagedResources();
        $result = self::$client->execute($query);
        $items = $result->getItems();
        $this->assertGreaterThanOrEqual(2, count($items));
    }

    /**
     * Extracts the encoding from the XML declaration of a file if present.
     *
     * @param string $file
     *
     * @return string|null
     */
    private static function getXmlEncoding(string $file): ?string
    {
        $encoding = null;

        $xml = file_get_contents($file);

        if (false !== $xml) {
            // discard UTF-8 Byte Order Mark
            if (pack('CCC', 0xEF, 0xBB, 0xBF) === substr($xml, 0, 3)) {
                $xml = substr($xml, 3);
            }

            // detect XML declaration
            if ('<?xml' === substr($xml, 0, 5)) {
                $declaration = substr($xml, 0, strpos($xml, '?>') + 2);

                // detect encoding attribute
                if (false !== $pos = strpos($declaration, 'encoding="')) {
                    $encoding = substr($declaration, $pos + 10, strpos($declaration, '"', $pos + 10) - $pos - 10);
                }
            }
        }

        return $encoding;
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
