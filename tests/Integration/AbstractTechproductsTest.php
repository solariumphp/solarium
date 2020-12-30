<?php

namespace Solarium\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\QueryTraits\GroupingTrait;
use Solarium\Component\QueryTraits\TermsTrait;
use Solarium\Component\Result\Grouping\FieldGroup;
use Solarium\Component\Result\Grouping\QueryGroup;
use Solarium\Component\Result\Grouping\Result as GroupingResult;
use Solarium\Component\Result\Grouping\ValueGroup;
use Solarium\Component\Result\Terms\Result as TermsResult;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Request;
use Solarium\Exception\HttpException;
use Solarium\Exception\RuntimeException;
use Solarium\Plugin\BufferedAdd\Event\AddDocument as BufferedAddAddDocumentEvent;
use Solarium\Plugin\BufferedAdd\Event\Events as BufferedAddEvents;
use Solarium\Plugin\BufferedAdd\Event\PostCommit as BufferedAddPostCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PostFlush as BufferedAddPostFlushEvent;
use Solarium\Plugin\BufferedAdd\Event\PreCommit as BufferedAddPreCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as BufferedAddPreFlushEvent;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Solarium\Support\Utility;

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
        $solrSpecVersion = $response->getData()['lucene']['solr-spec-version'];
        self::$solrVersion = (int) strstr($solrSpecVersion, '.', true);

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
            $dataDir = __DIR__.
                DIRECTORY_SEPARATOR.'..'.
                DIRECTORY_SEPARATOR.'..'.
                DIRECTORY_SEPARATOR.'lucene-solr'.
                DIRECTORY_SEPARATOR.'solr'.
                DIRECTORY_SEPARATOR.'example'.
                DIRECTORY_SEPARATOR.'exampledocs';
            foreach (glob($dataDir.DIRECTORY_SEPARATOR.'*.xml') as $file) {
                $update = self::$client->createUpdate();

                if (null !== $encoding = Utility::getXmlEncoding($file)) {
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
            $select->getHelper()->rangeQuery('store', '-90,-90', '90,90')
        );
        $result = self::$client->select($select);
        $this->assertSame(2, $result->getNumFound());
        $this->assertCount(2, $result);

        $select->setQuery(
            $select->getHelper()->rangeQuery('store', '-90,-180', '90,180')
        );
        $result = self::$client->select($select);
        $this->assertSame(14, $result->getNumFound());
        $this->assertCount(10, $result);

        // VS1GB400C3 costs 74.99, SP2514N costs 92.0, 0579B002 costs 179.99
        $select->setFields('id,price');
        $select->addSort('price', $select::SORT_ASC);
        $select->setQuery(
            $select->getHelper()->rangeQuery('price', 74.99, 179.99, [true, true])
        );
        $result = self::$client->select($select);
        $this->assertSame(3, $result->getNumFound());
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'VS1GB400C3',
            'price' => 74.99,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'SP2514N',
            'price' => 92.0,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => '0579B002',
            'price' => 179.99,
        ], $iterator->current()->getFields());

        $select->setQuery(
            $select->getHelper()->rangeQuery('price', 74.99, 179.99, [true, false])
        );
        $result = self::$client->select($select);
        $this->assertSame(2, $result->getNumFound());
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'VS1GB400C3',
            'price' => 74.99,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'SP2514N',
            'price' => 92.0,
        ], $iterator->current()->getFields());

        $select->setQuery(
            $select->getHelper()->rangeQuery('price', 74.99, 179.99, [false, true])
        );
        $result = self::$client->select($select);
        $this->assertSame(2, $result->getNumFound());
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'SP2514N',
            'price' => 92.0,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => '0579B002',
            'price' => 179.99,
        ], $iterator->current()->getFields());

        $select->setQuery(
            $select->getHelper()->rangeQuery('price', 74.99, 179.99, [false, false])
        );
        $result = self::$client->select($select);
        $this->assertSame(1, $result->getNumFound());
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'SP2514N',
            'price' => 92.0,
        ], $iterator->current()->getFields());
    }

    public function testFacetHighlightSpellcheckComponent()
    {
        $select = self::$client->createSelect();
        // In the techproducts example, the request handler "select" doesn't neither contain a spellcheck component nor
        // a highlighter or facets. But the "browse" request handler does.
        $select->setHandler('browse');
        // Search for misspelled "power cort".
        $select->setQuery('power cort');

        $spellcheck = $select->getSpellcheck();
        // Some spellcheck dictionaries need to be built first, but not on every request!
        $spellcheck->setBuild(true);
        // Order of suggestions is wrong on SolrCloud with spellcheck.extendedResults=false (SOLR-9060)
        $spellcheck->setExtendedResults(true);

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

    /**
     * The Grouping feature only works if groups are in the same shard. You must use the custom sharding feature to use the Grouping feature.
     *
     * @see https://cwiki.apache.org/confluence/display/solr/SolrCloud%20/#SolrCloud-KnownLimitations
     *
     * @group skip_for_solr_cloud
     */
    public function testGroupingComponent()
    {
        self::$client->registerQueryType('grouping', '\Solarium\Tests\Integration\GroupingTestQuery');
        /** @var GroupingTestQuery $select */
        $select = self::$client->createQuery('grouping');
        $select->setQuery('solr memory');
        $select->setFields('id');
        $select->addSort('manu_exact', SelectQuery::SORT_ASC);
        $grouping = $select->getGrouping();
        $grouping->setFields('manu_exact');
        $grouping->setSort('price asc');
        $result = self::$client->select($select);
        /** @var GroupingResult $groupingComponentResult */
        $groupingComponentResult = $result->getComponent(ComponentAwareQueryInterface::COMPONENT_GROUPING);

        /** @var FieldGroup $fieldGroup */
        $fieldGroup = $groupingComponentResult->getGroup('manu_exact');
        $this->assertSame(6, $fieldGroup->getMatches());
        $this->assertCount(5, $fieldGroup);
        $groupIterator = $fieldGroup->getIterator();

        /** @var ValueGroup $valueGroup */
        $valueGroup = $groupIterator->current();
        $this->assertSame(1, $valueGroup->getNumFound());
        $this->assertSame('A-DATA Technology Inc.', $valueGroup->getValue());
        $docIterator = $valueGroup->getIterator();
        /** @var Document $doc */
        $doc = $docIterator->current();
        $this->assertSame('VDBDB1A16', $doc->getFields()['id']);

        $groupIterator->next();
        $valueGroup = $groupIterator->current();
        $this->assertSame(1, $valueGroup->getNumFound());
        $this->assertSame('ASUS Computer Inc.', $valueGroup->getValue());
        $docIterator = $valueGroup->getIterator();
        $doc = $docIterator->current();
        $this->assertSame('EN7800GTX/2DHTV/256M', $doc->getFields()['id']);

        $groupIterator->next();
        $valueGroup = $groupIterator->current();
        $this->assertSame(1, $valueGroup->getNumFound());
        $this->assertSame('Apache Software Foundation', $valueGroup->getValue());
        $docIterator = $valueGroup->getIterator();
        $doc = $docIterator->current();
        $this->assertSame('SOLR1000', $doc->getFields()['id']);

        $groupIterator->next();
        $valueGroup = $groupIterator->current();
        $this->assertSame(1, $valueGroup->getNumFound());
        $this->assertSame('Canon Inc.', $valueGroup->getValue());
        $docIterator = $valueGroup->getIterator();
        $doc = $docIterator->current();
        $this->assertSame('0579B002', $doc->getFields()['id']);

        $groupIterator->next();
        $valueGroup = $groupIterator->current();
        $this->assertSame(2, $valueGroup->getNumFound());
        $this->assertSame('Corsair Microsystems Inc.', $valueGroup->getValue());
        $docIterator = $valueGroup->getIterator();
        $doc = $docIterator->current();
        $this->assertSame('VS1GB400C3', $doc->getFields()['id']);

        $select = self::$client->createQuery('grouping');
        $select->setQuery('memory');
        $select->setFields('id,price');
        $grouping = $select->getGrouping();
        $grouping->addQueries([
            $select->getHelper()->rangeQuery('price', 0, 99.99),
            $select->getHelper()->rangeQuery('price', 100, null),
        ]);
        $grouping->setLimit(3);
        $grouping->setSort('price desc');
        $result = self::$client->select($select);
        $groupingComponentResult = $result->getComponent(ComponentAwareQueryInterface::COMPONENT_GROUPING);

        /** @var QueryGroup $queryGroup */
        $queryGroup = $groupingComponentResult->getGroup('price:[0 TO 99.99]');
        $this->assertSame(5, $queryGroup->getMatches());
        $this->assertCount(1, $queryGroup);
        $docIterator = $queryGroup->getIterator();
        $doc = $docIterator->current();
        $this->assertSame([
            'id' => 'VS1GB400C3',
            'price' => 74.99,
        ], $doc->getFields());

        $queryGroup = $groupingComponentResult->getGroup('price:[100 TO *]');
        $this->assertSame(5, $queryGroup->getMatches());
        $this->assertCount(3, $queryGroup);
        $docIterator = $queryGroup->getIterator();
        $doc = $docIterator->current();
        $this->assertSame([
            'id' => 'EN7800GTX/2DHTV/256M',
            'price' => 479.95,
        ], $doc->getFields());
        $docIterator->next();
        $doc = $docIterator->current();
        $this->assertSame([
            'id' => 'TWINX2048-3200PRO',
            'price' => 185.0,
        ], $doc->getFields());
        $docIterator->next();
        $doc = $docIterator->current();
        $this->assertSame([
            'id' => '0579B002',
            'price' => 179.99,
        ], $doc->getFields());
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
        self::$client->registerQueryType('test', '\Solarium\Tests\Integration\TermsTestQuery');
        $select = self::$client->createQuery('test');

        // Setting distrib to true in a non cloud setup causes exceptions.
        if ($this instanceof AbstractCloudTest) {
            $select->setDistrib(true);
        }

        $terms = $select->getTerms();
        $terms->setFields('name');
        $result = self::$client->select($select);
        /** @var TermsResult $termsComponentResult */
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
        if (7 === self::$solrVersion && $this instanceof AbstractCloudTest) {
            // we still have to emulate a successful atomic update for the remainder of this test to pass
            $doc = $update->createDocument();
            $doc->setKey('id', 'solarium-test');
            $doc->setField('cat', 'modifier-add-distinct');
            $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
            $update->addDocument($doc);
            $update->addCommit(true, true);
            self::$client->update($update);
        } else {
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

    public function testNestedDocuments()
    {
        $data = [
            'id' => 'solarium-parent',
            'name' => 'Solarium Nested Document Parent',
            'cat' => ['solarium-nested-document', 'parent'],
            'children' => [
                [
                    'id' => 'solarium-child-1',
                    'name' => 'Solarium Nested Document Child 1',
                    'cat' => ['solarium-nested-document', 'child'],
                    'price' => 1.0,
                    'grandchildren' => [
                        [
                            'id' => 'solarium-grandchild-1-1',
                            'name' => 'Solarium Nested Document Grandchild 1.1',
                            'cat' => ['solarium-nested-document', 'grandchild'],
                            'price' => 1.1,
                        ],
                    ],
                ],
                [
                    'id' => 'solarium-child-2',
                    'name' => 'Solarium Nested Document Child 2',
                    'cat' => ['solarium-nested-document', 'child'],
                    'price' => 2.0,
                    'grandchildren' => [
                        [
                            'id' => 'solarium-grandchild-2-1',
                            'name' => 'Solarium Nested Document Grandchild 2.1',
                            'cat' => ['solarium-nested-document', 'grandchild'],
                            'price' => 2.1,
                        ],
                    ],
                ],
            ],
        ];

        $update = self::$client->createUpdate();
        $doc = $update->createDocument($data);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);

        // get all documents (parents and descendants) as a flat list
        $select = self::$client->createSelect();
        $select->setQuery('cat:solarium-nested-document');
        $select->setFields('id,name,price');
        $result = self::$client->select($select);
        $this->assertCount(5, $result);

        // without a sort, children are returned before their parents because they're added in that order to the underlying Lucene index
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-grandchild-1-1',
            'name' => 'Solarium Nested Document Grandchild 1.1',
            'price' => 1.1,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-child-1',
            'name' => 'Solarium Nested Document Child 1',
            'price' => 1.0,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-grandchild-2-1',
            'name' => 'Solarium Nested Document Grandchild 2.1',
            'price' => 2.1,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-child-2',
            'name' => 'Solarium Nested Document Child 2',
            'price' => 2.0,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-parent',
            'name' => 'Solarium Nested Document Parent',
        ], $iterator->current()->getFields());

        // in Solr 7, the [child] transformer returns all descendant documents in a flat list, this is covered in testAnonymouslyNestedDocuments()
        if (8 <= self::$solrVersion) {
            // get parent document with nested children as pseudo-fields
            $select->setQuery('id:solarium-parent');
            // 'id,[child]' is not enough, either * or the explicit names of the pseudo-fields are needed to actually include them
            $select->setFields('id,children,grandchildren,[child]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                'children' => [
                    [
                        'id' => 'solarium-child-1',
                        'grandchildren' => [
                            [
                                'id' => 'solarium-grandchild-1-1',
                            ],
                        ],
                    ],
                    [
                        'id' => 'solarium-child-2',
                        'grandchildren' => [
                            [
                                'id' => 'solarium-grandchild-2-1',
                            ],
                        ],
                    ],
                ],
            ], $iterator->current()->getFields());

            // only get descendant documents that match a filter
            $select->setFields('id,price,children,grandchildren,[child childFilter=price:2.1]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                'children' => [
                    [
                        'id' => 'solarium-child-2',
                        'price' => 2.0,
                        'grandchildren' => [
                            [
                                'id' => 'solarium-grandchild-2-1',
                                'price' => 2.1,
                            ],
                        ],
                    ],
                ],
            ], $iterator->current()->getFields());

            // limit nested path of child documents to be returned
            $select->setFields('id,children,grandchildren,[child childFilter=/children/*:*]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                'children' => [
                    [
                        'id' => 'solarium-child-1',
                    ],
                    [
                        'id' => 'solarium-child-2',
                    ],
                ],
            ], $iterator->current()->getFields());

            // limit number of child documents to be returned
            $select->setFields('id,children,grandchildren,[child limit=1]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                'children' => [
                    [
                        'id' => 'solarium-child-1',
                        'grandchildren' => [
                            [
                                'id' => 'solarium-grandchild-1-1',
                            ],
                        ],
                    ],
                ],
            ], $iterator->current()->getFields());

            // only return a subset of the top level fl parameter for the child documents
            $select->setFields('id,name,price,children,grandchildren,[child fl=id,price]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                'name' => 'Solarium Nested Document Parent',
                'children' => [
                    [
                        'id' => 'solarium-child-1',
                        'price' => 1.0,
                        'grandchildren' => [
                            [
                                'id' => 'solarium-grandchild-1-1',
                                'price' => 1.1,
                            ],
                        ],
                    ],
                    [
                        'id' => 'solarium-child-2',
                        'price' => 2.0,
                        'grandchildren' => [
                            [
                                'id' => 'solarium-grandchild-2-1',
                                'price' => 2.1,
                            ],
                        ],
                    ],
                ],
            ], $iterator->current()->getFields());
        }

        // parent query parser
        $select->setQuery('{!parent which="cat:parent"}id:solarium-child-1');
        $select->setFields('id');
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-parent',
        ], $iterator->current()->getFields());

        // child query parser
        $select->setQuery('{!child of="cat:parent"}id:solarium-parent');
        $select->setFields('id');
        $result = self::$client->select($select);
        $this->assertCount(4, $result);
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-grandchild-1-1',
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-child-1',
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-grandchild-2-1',
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-child-2',
        ], $iterator->current()->getFields());

        // in Solr 7, atomic updates of child documents aren't possible
        if (8 <= self::$solrVersion) {
            // atomic update: removing all child documents
            $doc = $update->createDocument();
            $doc->setKey('id', 'solarium-parent');
            $doc->setField('cat', 'updated');
            $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
            $doc->setField('children', []);
            $doc->setFieldModifier('children', $doc::MODIFIER_SET);
            $update->addDocument($doc);
            $update->addCommit(true, true);
            self::$client->update($update);
            $select->setQuery('id:solarium-parent');
            $select->setFields('id,name,cat,price,children,grandchildren,[child]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                'name' => 'Solarium Nested Document Parent',
                'cat' => [
                    'solarium-nested-document',
                    'parent',
                    'updated',
                ],
            ], $iterator->current()->getFields());

            // other atomic updates (replacing, adding, removing child documents) can't be executed through XML (SOLR-12677)
        }

        // cleanup
        $update = self::$client->createUpdate();
        // in Solr 7, the whole block of parent-children documents must be deleted together
        if (7 === self::$solrVersion) {
            $update->addDeleteQuery('cat:solarium-nested-document');
        }
        // in Solr 8, you can simply delete-by-ID using the id of the root document
        else {
            $update->addDeleteById('solarium-parent');
        }
        $update->addCommit(true, true);
        self::$client->update($update);
        $select->setQuery('cat:solarium-nested-document');
        $select->setFields('id');
        $result = self::$client->select($select);
        $this->assertCount(0, $result);
    }

    public function testAnonymouslyNestedDocuments()
    {
        $data = [
            'id' => 'solarium-parent',
            'name' => 'Solarium Nested Document Parent',
            'cat' => ['solarium-nested-document', 'parent'],
            '_childDocuments_' => [
                [
                    'id' => 'solarium-child-1',
                    'name' => 'Solarium Nested Document Child 1',
                    'cat' => ['solarium-nested-document', 'child'],
                    'price' => 1.0,
                ],
                [
                    'id' => 'solarium-child-2',
                    'name' => 'Solarium Nested Document Child 2',
                    'cat' => ['solarium-nested-document', 'child'],
                    'price' => 2.0,
                ],
            ],
        ];

        $update = self::$client->createUpdate();
        $doc = $update->createDocument($data);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);

        // get all documents (parents and descendants) as a flat list
        $select = self::$client->createSelect();
        $select->setQuery('cat:solarium-nested-document');
        $select->setFields('id,name,price');
        $result = self::$client->select($select);
        $this->assertCount(3, $result);

        // without a sort, children are returned before their parents because they're added in that order to the underlying Lucene index
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-child-1',
            'name' => 'Solarium Nested Document Child 1',
            'price' => 1.0,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-child-2',
            'name' => 'Solarium Nested Document Child 2',
            'price' => 2.0,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-parent',
            'name' => 'Solarium Nested Document Parent',
        ], $iterator->current()->getFields());

        // parent query parser
        $select->setQuery('{!parent which="cat:parent"}id:solarium-child-1');
        $select->setFields('id');
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-parent',
        ], $iterator->current()->getFields());

        // child query parser
        $select->setQuery('{!child of="cat:parent"}id:solarium-parent');
        $select->setFields('id');
        $result = self::$client->select($select);
        $this->assertCount(2, $result);
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-child-1',
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-child-2',
        ], $iterator->current()->getFields());

        // [child] transformer doesn't work for anonymous children when the schema includes a _nest_path_ in Solr 8
        if (7 === self::$solrVersion) {
            // get all child documents nested inside the parent document
            $select->setQuery('id:solarium-parent');
            $select->setFields('id,[child parentFilter=cat:parent fl=id]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                '_childDocuments_' => [
                    ['id' => 'solarium-child-1'],
                    ['id' => 'solarium-child-2'],
                ],
            ], $iterator->current()->getFields());

            // only get child documents that match a filter
            $select->setFields('id,[child parentFilter=cat:parent childFilter="price:[1.5 TO 2.5]" fl=id]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                '_childDocuments_' => [
                    ['id' => 'solarium-child-2'],
                ],
            ], $iterator->current()->getFields());

            // limit number of child documents to be returned
            $select->setFields('id,[child parentFilter=cat:parent limit=1 fl=id]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                '_childDocuments_' => [
                    ['id' => 'solarium-child-1'],
                ],
            ], $iterator->current()->getFields());

            // only return a subset of the top level fl parameter for the child documents
            $select->setFields('id,name,price,[child parentFilter=cat:parent fl=id,price]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                'name' => 'Solarium Nested Document Parent',
                '_childDocuments_' => [
                    [
                        'id' => 'solarium-child-1',
                        'price' => 1.0,
                    ],
                    [
                        'id' => 'solarium-child-2',
                        'price' => 2.0,
                    ],
                ],
            ], $iterator->current()->getFields());
        }

        // in Solr 7, atomic updates of child documents aren't possible
        // in SolrCloud mode, this fails more often with "Async exception during distributed update" than it succeeds
        // @todo get this sorted for distributed search when #908 is resolved
        if (8 <= self::$solrVersion && $this instanceof AbstractServerTest) {
            // atomic update: removing all child documents
            $doc = $update->createDocument();
            $doc->setKey('id', 'solarium-parent');
            $doc->setField('cat', 'updated');
            $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
            $doc->setField('_childDocuments_', []);
            $doc->setFieldModifier('_childDocuments_', $doc::MODIFIER_SET);
            $update->addDocument($doc);
            $update->addCommit(true, true);
            self::$client->update($update);
            // ensure the update was atomic ('name' must be unchanged, 'cat' must be updated)
            $select->setQuery('id:solarium-parent');
            $select->setFields('id,name,cat');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                'name' => 'Solarium Nested Document Parent',
                'cat' => [
                    'solarium-nested-document',
                    'parent',
                    'updated',
                ],
            ], $iterator->current()->getFields());
            // ensure child documents have been replaced (with nothing)
            $select->setQuery('{!child of="cat:parent"}id:solarium-parent');
            $select->setFields('id');
            $result = self::$client->select($select);
            $this->assertCount(0, $result);
        }

        // cleanup
        $update = self::$client->createUpdate();
        // in Solr 7, the whole block of parent-children documents must be deleted together
        if (7 === self::$solrVersion) {
            $update->addDeleteQuery('cat:solarium-nested-document');
        }
        // in Solr 8, you can simply delete-by-ID using the id of the root document
        else {
            $update->addDeleteById('solarium-parent');
        }
        $update->addCommit(true, true);
        self::$client->update($update);
        $select->setQuery('cat:solarium-nested-document');
        $select->setFields('id');
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

    public function testBufferedAdd()
    {
        $bufferSize = 10;
        $totalDocs = 25;

        $buffer = self::$client->getPlugin('bufferedadd');
        $buffer->setBufferSize($bufferSize);

        $update = self::$client->createUpdate();

        // can't be null at this point because phpstan analyses the ADD_DOCUMENT listener with this value even though it's never executed with it
        $document = $update->createDocument();
        $weight = 0;

        self::$client->getEventDispatcher()->addListener(
            BufferedAddEvents::ADD_DOCUMENT,
            function (BufferedAddAddDocumentEvent $event) use (&$document, &$weight) {
                $this->assertSame($document, $event->getDocument());
                $document->setField('weight', ++$weight);
            }
        );

        self::$client->getEventDispatcher()->addListener(
            BufferedAddEvents::PRE_FLUSH,
            function (BufferedAddPreFlushEvent $event) use ($bufferSize, &$document, &$weight) {
                static $i = 0;

                $buffer = $event->getBuffer();
                $this->assertCount($bufferSize, $buffer);
                $this->assertSame($document, end($buffer));

                $data = [
                    'id' => 'solarium-bufferedadd-preflush-'.++$i,
                    'cat' => 'solarium-bufferedadd',
                    'weight' => ++$weight,
                ];
                $buffer[] = self::$client->createUpdate()->createDocument($data);
                $event->setBuffer($buffer);
            }
        );

        self::$client->getEventDispatcher()->addListener(
            BufferedAddEvents::POST_FLUSH,
            function (BufferedAddPostFlushEvent $event) use ($bufferSize) {
                $result = $event->getResult();
                $this->assertSame(0, $result->getStatus());

                $query = $result->getQuery();
                $commands = $query->getCommands();
                $this->assertCount(1, $commands);
                $this->assertSame(UpdateQuery::COMMAND_ADD, $commands[0]->getType());
                // we added 1 document to the full buffer in PRE_FLUSH
                $this->assertCount($bufferSize + 1, $commands[0]->getDocuments());
            }
        );

        self::$client->getEventDispatcher()->addListener(
            BufferedAddEvents::PRE_COMMIT,
            function (BufferedAddPreCommitEvent $event) use ($bufferSize, $totalDocs, &$document, &$weight) {
                static $i = 0;

                $buffer = $event->getBuffer();
                $this->assertCount($totalDocs % $bufferSize, $event->getBuffer());
                $this->assertSame($document, end($buffer));

                $data = [
                    'id' => 'solarium-bufferedadd-precommit-'.++$i,
                    'cat' => 'solarium-bufferedadd',
                    'weight' => ++$weight,
                ];
                $buffer[] = self::$client->createUpdate()->createDocument($data);
                $event->setBuffer($buffer);
            }
        );

        self::$client->getEventDispatcher()->addListener(
            BufferedAddEvents::POST_COMMIT,
            function (BufferedAddPostCommitEvent $event) use ($bufferSize, $totalDocs) {
                $result = $event->getResult();
                $this->assertSame(0, $result->getStatus());

                $query = $result->getQuery();
                $commands = $query->getCommands();
                $this->assertCount(2, $commands);
                $this->assertSame(UpdateQuery::COMMAND_ADD, $commands[0]->getType());
                $this->assertSame(UpdateQuery::COMMAND_COMMIT, $commands[1]->getType());
                // we added 1 document to the remaining buffer in PRE_COMMIT
                $this->assertCount(($totalDocs % $bufferSize) + 1, $commands[0]->getDocuments());
            }
        );

        $data = [
            'id' => 'solarium-bufferedadd-0',
            'cat' => 'solarium-bufferedadd',
        ];
        $document = $update->createDocument($data);
        $buffer->addDocument($document);
        $this->assertCount(1, $buffer->getDocuments());
        $buffer->clear();
        $this->assertCount(0, $buffer->getDocuments());

        for ($i = 1; $i <= $totalDocs; ++$i) {
            $data = [
                'id' => 'solarium-bufferedadd-'.$i,
                'cat' => 'solarium-bufferedadd',
            ];
            $document = $update->createDocument($data);
            $buffer->addDocument($document);
        }

        $buffer->commit(null, true, true);

        $select = self::$client->createSelect();
        $select->setQuery('cat:solarium-bufferedadd');
        $select->addSort('weight', $select::SORT_ASC);
        $select->setFields('id');
        $select->setRows(28);
        $result = self::$client->select($select);
        $this->assertSame(28, $result->getNumFound());

        $ids = [];
        /** @var \Solarium\QueryType\Select\Result\Document $document */
        foreach ($result as $document) {
            $ids[] = $document->id;
        }

        $this->assertEquals([
            'solarium-bufferedadd-1',
            'solarium-bufferedadd-2',
            'solarium-bufferedadd-3',
            'solarium-bufferedadd-4',
            'solarium-bufferedadd-5',
            'solarium-bufferedadd-6',
            'solarium-bufferedadd-7',
            'solarium-bufferedadd-8',
            'solarium-bufferedadd-9',
            'solarium-bufferedadd-10',
            'solarium-bufferedadd-preflush-1',
            'solarium-bufferedadd-11',
            'solarium-bufferedadd-12',
            'solarium-bufferedadd-13',
            'solarium-bufferedadd-14',
            'solarium-bufferedadd-15',
            'solarium-bufferedadd-16',
            'solarium-bufferedadd-17',
            'solarium-bufferedadd-18',
            'solarium-bufferedadd-19',
            'solarium-bufferedadd-20',
            'solarium-bufferedadd-preflush-2',
            'solarium-bufferedadd-21',
            'solarium-bufferedadd-22',
            'solarium-bufferedadd-23',
            'solarium-bufferedadd-24',
            'solarium-bufferedadd-25',
            'solarium-bufferedadd-precommit-1',
            ], $ids);

        // cleanup
        $update->addDeleteQuery('cat:solarium-bufferedadd');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertSame(0, $result->getNumFound());
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
        $extract->setCommit(true);
        $extract->setCommitWithin(0);
        $extract->setOmitHeader(false);

        // add PDF document
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testpdf.pdf');
        $doc = $extract->createDocument();
        $doc->id = 'extract-test-1-pdf';
        $doc->cat = ['extract-test'];
        $extract->setDocument($doc);
        self::$client->extract($extract);

        // add HTML document
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testhtml.html');
        $doc = $extract->createDocument();
        $doc->id = 'extract-test-2-html';
        $doc->cat = ['extract-test'];
        $extract->setDocument($doc);
        self::$client->extract($extract);

        // now get the documents and check the contents
        $select = self::$client->createSelect();
        $select->setQuery('cat:extract-test');
        $select->addSort('id', $select::SORT_ASC);
        $selectResult = self::$client->select($select);
        $this->assertCount(2, $selectResult);
        $iterator = $selectResult->getIterator();

        /** @var Document $document */
        $document = $iterator->current();
        $this->assertSame('application/pdf', $document['content_type'][0], 'Written document does not contain extracted content type');
        $this->assertSame('PDF Test', trim($document['content'][0]), 'Written document does not contain extracted result');
        $iterator->next();
        $document = $iterator->current();
        $this->assertSame('text/html; charset=UTF-8', $document['content_type'][0], 'Written document does not contain extracted content type');
        $this->assertSame('HTML Test Title', $document['title'][0], 'Written document does not contain extracted title');
        $this->assertRegExp('/^HTML Test Title\s+HTML Test Body$/', trim($document['content'][0]), 'Written document does not contain extracted result');

        // now cleanup the document the have the initial index state
        $update = self::$client->createUpdate();
        $update->addDeleteQuery('cat:extract-test');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);
    }

    public function testExtractOnlyText()
    {
        $query = self::$client->createExtract();
        $query->setExtractOnly(true);
        $query->setExtractFormat($query::EXTRACT_FORMAT_TEXT);
        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testpdf.pdf');

        $response = self::$client->extract($query);
        $this->assertSame('PDF Test', trim($response->getData()['testpdf.pdf']), 'Can not extract the plain content from the PDF file');
        $this->assertSame('PDF Test', trim($response->getData()['file']), 'Can not extract the plain content from the PDF file');

        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testhtml.html');

        $response = self::$client->extract($query);
        $this->assertRegExp('/^HTML Test Title\s+HTML Test Body$/', trim($response->getData()['testhtml.html']), 'Can not extract the plain content from the HTML file');
        $this->assertRegExp('/^HTML Test Title\s+HTML Test Body$/', trim($response->getData()['file']), 'Can not extract the plain content from the HTML file');
    }

    public function testExtractOnlyXml()
    {
        $query = self::$client->createExtract();
        $query->setExtractOnly(true);
        $query->setExtractFormat($query::EXTRACT_FORMAT_XML);
        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testpdf.pdf');

        $response = self::$client->extract($query);
        $this->assertSame(0, strpos($response->getData()['testpdf.pdf'], '<?xml version="1.0" encoding="UTF-8"?>'), 'Extracted content from the PDF file is not XML');
        $this->assertSame(0, strpos($response->getData()['file'], '<?xml version="1.0" encoding="UTF-8"?>'), 'Extracted content from the PDF file is not XML');
        $this->assertNotFalse(strpos($response->getData()['testpdf.pdf'], '<p>PDF Test</p>'), 'Extracted content from the PDF file not found in XML');
        $this->assertNotFalse(strpos($response->getData()['file'], '<p>PDF Test</p>'), 'Extracted content from the PDF file not found in XML');

        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testhtml.html');

        $response = self::$client->extract($query);
        $this->assertSame(0, strpos($response->getData()['testhtml.html'], '<?xml version="1.0" encoding="UTF-8"?>'), 'Extracted content from the HTML file is not XML');
        $this->assertSame(0, strpos($response->getData()['file'], '<?xml version="1.0" encoding="UTF-8"?>'), 'Extracted content from the HTML file is not XML');
        $this->assertNotFalse(strpos($response->getData()['testhtml.html'], '<title>HTML Test Title</title>'), 'Extracted title from the HTML file not found in XML');
        $this->assertNotFalse(strpos($response->getData()['file'], '<title>HTML Test Title</title>'), 'Extracted title from the HTML file not found in XML');
        $this->assertNotFalse(strpos($response->getData()['testhtml.html'], '<p>HTML Test Body</p>'), 'Extracted body from the HTML file not found in XML');
        $this->assertNotFalse(strpos($response->getData()['file'], '<p>HTML Test Body</p>'), 'Extracted body from the HTML file not found in XML');
    }

    /**
     * Test extraction from files that contain special characters in both filename and content.
     */
    public function testExtractSpecialCharacters()
    {
        $query = self::$client->createExtract();
        $query->setExtractOnly(true);
        $query->setExtractFormat($query::EXTRACT_FORMAT_TEXT);
        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test us-ascii !#$%&\'()+,-.;=@[]^_`{}~.txt');

        // the file contains all 128 codepoints of the full 7-bit US-ASCII table, but we only test for printable characters
        $printableASCII = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~';

        $response = self::$client->extract($query);
        $this->assertNotFalse(strpos($response->getData()['test us-ascii !#$%&\'()+,-.;=@[]^_`{}~.txt'], $printableASCII), 'Can not extract from file with US-ASCII characters');
        $this->assertNotFalse(strpos($response->getData()['file'], $printableASCII), 'Can not extract from file with US-ASCII characters');

        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test utf-8 αβγ абв אԱა.txt');

        // the file contains some example text from https://www.w3.org/2001/06/utf-8-test/UTF-8-demo.html
        $sampleUTF8 = '£©µÀÆÖÞßéöÿ ΑΒΓΔΩαβγδω АБВГДабвгд ﬁ�⑀₂ἠḂӥẄɐː⍎אԱა';

        $response = self::$client->extract($query);
        $this->assertSame($sampleUTF8, trim($response->getData()['test utf-8 αβγ абв אԱა.txt']), 'Can not extract from file with UTF-8 characters');
        $this->assertSame($sampleUTF8, trim($response->getData()['file']), 'Can not extract from file with UTF-8 characters');

        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test utf-8 fəˈnɛtık.txt');

        // the file contains a phonetic example from https://www.w3.org/2001/06/utf-8-test/UTF-8-demo.html
        $samplePhonetic = 'ði ıntəˈnæʃənəl fəˈnɛtık əsoʊsiˈeıʃn';

        $response = self::$client->extract($query);
        $this->assertSame($samplePhonetic, trim($response->getData()['test utf-8 fəˈnɛtık.txt']), 'Can not extract from file with phonetic characters');
        $this->assertSame($samplePhonetic, trim($response->getData()['file']), 'Can not extract from file with phonetic characters');

        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test utf-8 コンニチハ.txt');

        // the file contains a Katakana example from https://www.w3.org/2001/06/utf-8-test/UTF-8-demo.html
        $sampleKatakana = 'コンニチハ';

        $response = self::$client->extract($query);
        $this->assertSame($sampleKatakana, trim($response->getData()['test utf-8 コンニチハ.txt']), 'Can not extract from file with Katakana characters');
        $this->assertSame($sampleKatakana, trim($response->getData()['file']), 'Can not extract from file with Katakana characters');

        // test with a file that specifies the encoding, Tika has a hard time telling ISO-8859-* and Windows-* sets apart on plain text with this little data
        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test iso-8859-1 ¡¢£¤¥¦§¨©ª«¬.xml');

        // the file contains the printable characters from ISO-8859-1
        $printableISO88591 = ' ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ';

        $response = self::$client->extract($query);
        $this->assertSame($printableISO88591, trim($response->getData()['test iso-8859-1 ¡¢£¤¥¦§¨©ª«¬.xml']), 'Can not extract from file with ISO-8859-1 encoding');
        $this->assertSame($printableISO88591, trim($response->getData()['file']), 'Can not extract from file with ISO-8859-1 encoding');

        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test gb18030 这份文件是很有光泽.txt');

        // the file contains a GB18030 example from the techproducts sample set
        $sampleGB18030 = '这份文件是很有光泽';

        $response = self::$client->extract($query);
        $this->assertSame($sampleGB18030, trim($response->getData()['test gb18030 这份文件是很有光泽.txt']), 'Can not extract from file with GB18030 encoding');
        $this->assertSame($sampleGB18030, trim($response->getData()['file']), 'Can not extract from file with GB18030 encoding');
    }

    public function testExtractInvalidFile()
    {
        $extract = self::$client->createExtract();
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'nosuchfile');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Extract query file path/url invalid or not available: '.__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'nosuchfile');
        self::$client->extract($extract);
    }

    public function testV2Api()
    {
        if (7 <= self::$solrVersion) {
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
        $add = $query->createCommand($query::COMMAND_ADD);
        $add->setStopwords([$term]);
        $query->setCommand($add);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if single stopword exists
        $exists = $query->createCommand($query::COMMAND_EXISTS);
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
        $delete = $query->createCommand($query::COMMAND_DELETE);
        $delete->setTerm($term);
        $query->setCommand($delete);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if stopword is gone
        $this->expectException(HttpException::class);
        $exists = $query->createCommand($query::COMMAND_EXISTS);
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
        $create = $query->createCommand($query::COMMAND_CREATE);
        $query->setCommand($create);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Whatever happens next ...
        try {
            // Configure the new list to be case sensitive
            $initArgs = $query->createInitArgs();
            $initArgs->setIgnoreCase(false);
            $config = $query->createCommand($query::COMMAND_CONFIG);
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
            $add = $query->createCommand($query::COMMAND_ADD);
            $add->setStopwords([$term]);
            $query->setCommand($add);
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if stopword exists in its original lowercase form
            $exists = $query->createCommand($query::COMMAND_EXISTS);
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
            $remove = $query->createCommand($query::COMMAND_REMOVE);
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
        $add = $query->createCommand($query::COMMAND_ADD);
        $synonyms = new Synonyms();
        $synonyms->setTerm($term);
        $synonyms->setSynonyms(['managed_synonym', 'synonym_test']);
        $add->setSynonyms($synonyms);
        $query->setCommand($add);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if single synonym exists
        $exists = $query->createCommand($query::COMMAND_EXISTS);
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
        $delete = $query->createCommand($query::COMMAND_DELETE);
        $delete->setTerm($term);
        $query->setCommand($delete);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Check if synonyms are gone
        $this->expectException(HttpException::class);
        $exists = $query->createCommand($query::COMMAND_EXISTS);
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
        $create = $query->createCommand($query::COMMAND_CREATE);
        $query->setCommand($create);
        $result = self::$client->execute($query);
        $this->assertEquals(200, $result->getResponse()->getStatusCode());

        // Whatever happens next ...
        try {
            // Configure the new map to be case sensitive and use the 'solr' format
            $initArgs = $query->createInitArgs();
            $initArgs->setIgnoreCase(false);
            $initArgs->setFormat($initArgs::FORMAT_SOLR);
            $config = $query->createCommand($query::COMMAND_CONFIG);
            $config->setInitArgs($initArgs);
            $query->setCommand($config);
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check the configuration
            $query->removeCommand();
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());
            $this->assertFalse($result->isIgnoreCase());
            $this->assertEquals($initArgs::FORMAT_SOLR, $result->getFormat());

            // Check if we can add to it
            $add = $query->createCommand($query::COMMAND_ADD);
            $synonyms = new Synonyms();
            $synonyms->setTerm($term);
            $synonyms->setSynonyms(['managed_synonym', 'synonym_test']);
            $add->setSynonyms($synonyms);
            $query->setCommand($add);
            $result = self::$client->execute($query);
            $this->assertEquals(200, $result->getResponse()->getStatusCode());

            // Check if synonym exists in its original lowercase form
            $exists = $query->createCommand($query::COMMAND_EXISTS);
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
            $remove = $query->createCommand($query::COMMAND_REMOVE);
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
}

class GroupingTestQuery extends SelectQuery
{
    use GroupingTrait;
}

class TermsTestQuery extends SelectQuery
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
