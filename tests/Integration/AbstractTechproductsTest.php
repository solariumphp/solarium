<?php

namespace Solarium\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Highlighting\Highlighting;
use Solarium\Component\QueryTraits\GroupingTrait;
use Solarium\Component\QueryTraits\TermsTrait;
use Solarium\Component\Result\Grouping\FieldGroup;
use Solarium\Component\Result\Grouping\QueryGroup;
use Solarium\Component\Result\Grouping\Result as GroupingResult;
use Solarium\Component\Result\Grouping\ValueGroup;
use Solarium\Component\Result\Terms\Result as TermsResult;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\Helper;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Exception\HttpException;
use Solarium\Exception\RuntimeException;
use Solarium\Exception\UnexpectedValueException;
use Solarium\Plugin\BufferedAdd\Event\AddDocument as BufferedAddAddDocumentEvent;
use Solarium\Plugin\BufferedAdd\Event\Events as BufferedAddEvents;
use Solarium\Plugin\BufferedAdd\Event\PostCommit as BufferedAddPostCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PostFlush as BufferedAddPostFlushEvent;
use Solarium\Plugin\BufferedAdd\Event\PreCommit as BufferedAddPreCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as BufferedAddPreFlushEvent;
use Solarium\Plugin\BufferedDelete\Event\AddDeleteById as BufferedDeleteAddDeleteByIdEvent;
use Solarium\Plugin\BufferedDelete\Event\AddDeleteQuery as BufferedDeleteAddDeleteQueryEvent;
use Solarium\Plugin\BufferedDelete\Event\Events as BufferedDeleteEvents;
use Solarium\Plugin\PrefetchIterator;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as AbstractManagedResourceQuery;
use Solarium\QueryType\ManagedResources\Query\Command\AbstractAdd as AbstractAddCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords as StopwordsQuery;
use Solarium\QueryType\ManagedResources\Query\Synonyms as SynonymsQuery;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resource as ResourceRequestBuilder;
use Solarium\QueryType\ManagedResources\Result\Resources\Resource as ResourceResultItem;
use Solarium\QueryType\ManagedResources\Result\Synonyms\Synonyms as SynonymsResultItem;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\Result as SelectResult;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Solarium\QueryType\Update\RequestBuilder as UpdateRequestBuilder;
use Solarium\Support\Utility;
use Symfony\Contracts\EventDispatcher\Event;

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
     * Major Solr version.
     *
     * @var int
     */
    protected static $solrVersion;

    /**
     * Solr running on Windows?
     *
     * SOLR-15895 has to be avoided when testing against Solr on Windows.
     *
     * @var bool
     */
    protected static $isSolrOnWindows;

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
        $system = $response->getData();

        $solrSpecVersion = $system['lucene']['solr-spec-version'];
        self::$solrVersion = (int) strstr($solrSpecVersion, '.', true);

        $systemName = $system['system']['name'];
        self::$isSolrOnWindows = 0 === strpos($systemName, 'Windows');

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

    public function testSelect(): SelectResult
    {
        $select = self::$client->createSelect();
        $select->setSorts(['id' => SelectQuery::SORT_ASC]);
        $result = self::$client->select($select);
        $this->assertSame(32, $result->getNumFound());
        $this->assertCount(10, $result);

        $ids = [];
        /** @var Document $document */
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

        return $result;
    }

    /**
     * @depends testSelect
     *
     * @param SelectResult $result
     */
    public function testJsonSerializeSelectResult(SelectResult $result)
    {
        $expectedJson = $result->getResponse()->getBody();

        // this only calls SelectResult::jsonSerialize() which gets the document data from the parsed response
        $json = json_encode($result);
        $this->assertJsonStringEqualsJsonString($expectedJson, $json);

        // this calls Document::jsonSerialize() on every document instead
        $documents = json_encode($result->getDocuments());
        $this->assertStringContainsString($documents, $json);
    }

    /**
     * @see https://solr.apache.org/guide/the-standard-query-parser.html#escaping-special-characters
     */
    public function testEscapes()
    {
        $escapeChars = [' ', '+', '-', '&&', '||', '!', '(', ')', '{', '}', '[', ']', '^', '"', '~', '*', '?', ':', '/', '\\'];
        $cat = [implode('', $escapeChars)];

        foreach ($escapeChars as $char) {
            $cat[] = 'a'.$char.'b';
        }

        $update = self::$client->createUpdate();
        $doc = $update->createDocument();
        $doc->setField('id', 'solarium-test-escapes');
        $doc->setField('name', 'Solarium Test Escapes');
        $doc->setField('cat', $cat);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);

        $select = self::$client->createSelect();
        $select->setQuery('id:%T1%', ['solarium-test-escapes']);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame($cat, $result->getIterator()->current()->getFields()['cat']);

        foreach ($escapeChars as $char) {
            // as term
            $select->setQuery('cat:%T1%', ['a'.$char.'b']);
            $result = self::$client->select($select);
            $this->assertCount(1, $result, $msg = sprintf('Failure with term containing \'%s\'.', $char));
            $this->assertSame('solarium-test-escapes', $result->getIterator()->current()->getFields()['id'], $msg);

            // as phrase
            $select->setQuery('cat:%P1%', ['a'.$char.'b']);
            $result = self::$client->select($select);
            $this->assertCount(1, $result, $msg = sprintf('Failure with phrase containing \'%s\'.', $char));
            $this->assertSame('solarium-test-escapes', $result->getIterator()->current()->getFields()['id'], $msg);
        }

        // cleanup
        $update->addDeleteQuery('cat:%T1%', [$cat[0]]);
        $update->addCommit(true, true);
        self::$client->update($update);
        $select->setQuery('id:solarium-test-escapes');
        $result = self::$client->select($select);
        $this->assertCount(0, $result);
    }

    /**
     * @see https://github.com/solariumphp/solarium/issues/974
     * @see https://solr.apache.org/guide/local-parameters-in-queries.html#basic-syntax-of-local-parameters
     */
    public function testLocalParamValueEscapes()
    {
        $categories = [
            'solarium-test-localparamvalue-escapes',
            'space: the final frontier',
            "'single-quote",
            '"double-quote',
            'escaped backslash \\',
            'right-curly-bracket}',
            // \ in and of itself and {! don't need escaping
            'unescaped-backslash-\\',
            '{!left-curly-bracket',
        ];

        $update = self::$client->createUpdate();
        $doc = $update->createDocument();
        $doc->setField('id', 'solarium-test-localparamvalue-escapes');
        $doc->setField('name', 'Solarium Test Local Param Value Escapes');
        $doc->setField('cat', $categories);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);

        $select = self::$client->createSelect();
        $select->setRows(0);
        $facetSet = $select->getFacetSet();

        // without escaping, ' " } cause an error
        foreach ($categories as $cat) {
            $facetSet->createFacetField($cat)->setField('cat')->setContains($cat);
        }

        // without escaping, 'electronics and computer' would match 3 values that contain 'electronics' in techproducts
        $facetSet->createFacetField('electronics')->setField('cat')->setContains('electronics and computer');

        // without escaping, a space can be abused for Local Parameter Injection
        $facetSet->createFacetField('ELECTRONICS')->setField('cat')->setContains('ELECTRONICS')->setContainsIgnoreCase(true);
        $facetSet->createFacetField('ELECTRONICS_LPI')->setField('cat')->setContains('ELECTRONICS facet.contains.ignoreCase=true');

        $result = self::$client->select($select);

        foreach ($categories as $cat) {
            $facet = $result->getFacetSet()->getFacet($cat);
            $this->assertEquals([$cat => 1], $facet->getValues());
        }

        $facet = $result->getFacetSet()->getFacet('electronics');
        $this->assertEquals(['electronics and computer1' => 1], $facet->getValues());

        $facet = $result->getFacetSet()->getFacet('ELECTRONICS');
        $this->assertCount(3, $facet);
        $facet = $result->getFacetSet()->getFacet('ELECTRONICS_LPI');
        $this->assertCount(0, $facet);

        // cleanup
        $update->addDeleteById('solarium-test-localparamvalue-escapes');
        $update->addCommit(true, true);
        self::$client->update($update);
        $select->setQuery('id:solarium-test-localparamvalue-escapes');
        $result = self::$client->select($select);
        $this->assertCount(0, $result);
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

        // MA147LL/A was manufactured on 2005-10-12T08:00:00Z, F8V7067-APL-KIT on 2005-08-01T16:30:25Z
        $select->setFields('id,manufacturedate_dt');
        $select->addSort('manufacturedate_dt', $select::SORT_DESC);
        $select->setQuery(
            $select->getHelper()->rangeQuery('manufacturedate_dt', '2005-01-01T00:00:00Z', '2005-12-31T23:59:59Z')
        );
        $result = self::$client->select($select);
        $this->assertSame(2, $result->getNumFound());
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'MA147LL/A',
            'manufacturedate_dt' => '2005-10-12T08:00:00Z',
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'F8V7067-APL-KIT',
            'manufacturedate_dt' => '2005-08-01T16:30:25Z',
        ], $iterator->current()->getFields());

        // VS1GB400C3 costs 74.99, SP2514N costs 92.0, 0579B002 costs 179.99
        $select->setFields('id,price');
        $select->clearSorts()->addSort('price', $select::SORT_ASC);
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
        // a highlighter or facets. But the self-defined "componentdemo" request handler does.
        $select->setHandler('componentdemo');
        // Search for misspelled "power cort".
        $select->setQuery('power cort');

        $spellcheck = $select->getSpellcheck();
        // Some spellcheck dictionaries need to be built first, but not on every request!
        $spellcheck->setBuild(true);
        $spellcheck->setCount(5);
        $spellcheck->setAlternativeTermCount(2);
        // Order of suggestions is wrong on SolrCloud with spellcheck.extendedResults=false (SOLR-9060)
        $spellcheck->setExtendedResults(true);
        $spellcheck->setCollate(true);
        $spellcheck->setCollateExtendedResults(true);
        $spellcheck->setMaxCollationTries(5);
        $spellcheck->setMaxCollations(3);

        $result = self::$client->select($select);
        $this->assertSame(0, $result->getNumFound());
        $this->assertFalse($result->getSpellcheck()->getCorrectlySpelled());

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
        $this->assertFalse($result->getSpellcheck()->getCorrectlySpelled());

        $this->assertSame(
            [
                'power' => 'power',
                'cort' => 'cord',
            ],
            $result->getSpellcheck()->getCollations()[0]->getCorrections()
        );

        $select->setQuery('power cord');

        $highlighting = $select->getHighlighting();
        $highlighting->setMethod(Highlighting::METHOD_ORIGINAL);
        $highlighting->setSimplePrefix('<b>')->setSimplePostfix('</b>');

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
            ['Belkin Mobile <b>Power</b> <b>Cord</b> for iPod w/ Dock'],
            $result->getHighlighting()->getResult('F8V7067-APL-KIT')->getField('name')
        );

        $this->assertSame(
            [
                'features' => ['car <b>power</b> adapter, white'],
                'name' => ['Belkin Mobile <b>Power</b> <b>Cord</b> for iPod w/ Dock'],
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
     * @testWith ["METHOD_UNIFIED"]
     *           ["METHOD_ORIGINAL"]
     *           ["METHOD_FASTVECTOR"]
     */
    public function testHighlightingComponentMethods(string $method)
    {
        $select = self::$client->createSelect();
        // The self-defined "componentdemo" request handler has a highlighting component.
        $select->setHandler('componentdemo');
        $select->setQuery('id:F8V7067-APL-KIT');

        $highlighting = $select->getHighlighting();
        $highlighting->setMethod(\constant(Highlighting::class.'::'.$method));
        $highlighting->setFields('name, features');
        $highlighting->getField('features')->setSimplePrefix('<u class="hl">')->setSimplePostfix('</u>');
        $highlighting->setQuery('(power cord) OR (power adapter)');
        $highlighting->setQueryParser('edismax');
        $highlighting->setSimplePrefix('<b>')->setSimplePostfix('</b>');

        // We don't set HTML encoding for ease of comparison, this can open an XSS attack vector.
        // Make sure that you set it by default in solrconfig.xml or on every request if required!
        // $highlighting->setEncoder(Highlighting::ENCODER_HTML);

        $result = self::$client->select($select);
        $this->assertSame(1, $result->getNumFound());

        foreach ($result as $document) {
            $this->assertSame('F8V7067-APL-KIT', $document->id);
        }

        $this->assertSame(
            ['Belkin Mobile <b>Power</b> <b>Cord</b> for iPod w/ Dock'],
            $result->getHighlighting()->getResult('F8V7067-APL-KIT')->getField('name')
        );

        $this->assertSame(
            ['car <u class="hl">power</u> <u class="hl">adapter</u>, white'],
            $result->getHighlighting()->getResult('F8V7067-APL-KIT')->getField('features')
        );

        $this->assertSame(
            [
                'name' => ['Belkin Mobile <b>Power</b> <b>Cord</b> for iPod w/ Dock'],
                'features' => ['car <u class="hl">power</u> <u class="hl">adapter</u>, white'],
            ],
            $result->getHighlighting()->getResult('F8V7067-APL-KIT')->getFields()
        );
    }

    /**
     * @see https://github.com/solariumphp/solarium/issues/184
     */
    public function testSpellCheckComponentWithSameWordMisspelledMultipleTimes()
    {
        $select = self::$client->createSelect();
        $select->setHandler('spell');
        $select->getEDisMax()->setMinimumMatch('100%');
        $select->setQuery('power cort cort');

        $spellcheck = $select->getSpellcheck();
        // Some spellcheck dictionaries need to be built first, but not on every request!
        $spellcheck->setBuild(true);
        // Order of suggestions is wrong on SolrCloud with spellcheck.extendedResults=false (SOLR-9060)
        $spellcheck->setExtendedResults(true);

        $result = self::$client->select($select);
        $this->assertSame(0, $result->getNumFound());
        $this->assertFalse($result->getSpellcheck()->getCorrectlySpelled());

        $this->assertSame(
            [
                'power' => 'power',
                'cort' => [
                    'cord',
                    'cord',
                ],
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
        $this->assertFalse($result->getSpellcheck()->getCorrectlySpelled());

        $this->assertSame(
            [
                'power' => 'power',
                'cort' => [
                    'cord',
                    'cord',
                ],
            ],
            $result->getSpellcheck()->getCollations()[0]->getCorrections()
        );
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
        self::$client->registerQueryType('grouping', GroupingTestQuery::class);
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
        $this->assertSame(0, $valueGroup->getStart());
        $this->assertSame('A-DATA Technology Inc.', $valueGroup->getValue());
        $docIterator = $valueGroup->getIterator();
        /** @var Document $doc */
        $doc = $docIterator->current();
        $this->assertSame('VDBDB1A16', $doc->getFields()['id']);

        $groupIterator->next();
        $valueGroup = $groupIterator->current();
        $this->assertSame(1, $valueGroup->getNumFound());
        $this->assertSame(0, $valueGroup->getStart());
        $this->assertSame('ASUS Computer Inc.', $valueGroup->getValue());
        $docIterator = $valueGroup->getIterator();
        $doc = $docIterator->current();
        $this->assertSame('EN7800GTX/2DHTV/256M', $doc->getFields()['id']);

        $groupIterator->next();
        $valueGroup = $groupIterator->current();
        $this->assertSame(1, $valueGroup->getNumFound());
        $this->assertSame(0, $valueGroup->getStart());
        $this->assertSame('Apache Software Foundation', $valueGroup->getValue());
        $docIterator = $valueGroup->getIterator();
        $doc = $docIterator->current();
        $this->assertSame('SOLR1000', $doc->getFields()['id']);

        $groupIterator->next();
        $valueGroup = $groupIterator->current();
        $this->assertSame(1, $valueGroup->getNumFound());
        $this->assertSame(0, $valueGroup->getStart());
        $this->assertSame('Canon Inc.', $valueGroup->getValue());
        $docIterator = $valueGroup->getIterator();
        $doc = $docIterator->current();
        $this->assertSame('0579B002', $doc->getFields()['id']);

        $groupIterator->next();
        $valueGroup = $groupIterator->current();
        $this->assertSame(2, $valueGroup->getNumFound());
        $this->assertSame(0, $valueGroup->getStart());
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
        $this->assertSame(1, $queryGroup->getNumFound());
        $this->assertSame(0, $queryGroup->getStart());
        $this->assertCount(1, $queryGroup);
        $docIterator = $queryGroup->getIterator();
        $doc = $docIterator->current();
        $this->assertSame([
            'id' => 'VS1GB400C3',
            'price' => 74.99,
        ], $doc->getFields());

        $queryGroup = $groupingComponentResult->getGroup('price:[100 TO *]');
        $this->assertSame(5, $queryGroup->getMatches());
        $this->assertSame(3, $queryGroup->getNumFound());
        $this->assertSame(0, $queryGroup->getStart());
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

    /**
     * Test fix for maxScore being returned as "NaN" when group.query doesn't match any docs.
     *
     * Skipped for SolrCloud because maxScore is included in distributed search results even if score is not requested (SOLR-6612).
     * This makes the test fail on SolrCloud for queries that don't fetch a score and thus aren't affected by SOLR-13839.
     *
     * @group skip_for_solr_cloud
     *
     * @see https://issues.apache.org/jira/browse/SOLR-13839
     * @see https://issues.apache.org/jira/browse/SOLR-6612
     */
    public function testGroupingComponentFixForSolr13839()
    {
        self::$client->registerQueryType('grouping', GroupingTestQuery::class);
        /** @var GroupingTestQuery $select */
        $select = self::$client->createQuery('grouping');
        // without score in the fl parameter, result groups don't have a maxScore
        $select->setFields('id');
        $grouping = $select->getGrouping();
        $grouping->addQueries([
            'cat:memory',
            'cat:no-such-cat',
        ]);
        $result = self::$client->select($select);
        $groupingComponentResult = $result->getComponent(ComponentAwareQueryInterface::COMPONENT_GROUPING);

        /** @var QueryGroup $queryGroup */
        $queryGroup = $groupingComponentResult->getGroup('cat:memory');
        $this->assertSame(32, $queryGroup->getMatches());
        $this->assertSame(3, $queryGroup->getNumFound());
        $this->assertNull($queryGroup->getMaximumScore());

        $queryGroup = $groupingComponentResult->getGroup('cat:no-such-cat');
        $this->assertSame(32, $queryGroup->getMatches());
        $this->assertSame(0, $queryGroup->getNumFound());
        $this->assertNull($queryGroup->getMaximumScore());

        // with score in the fl parameter, result groups have a maxScore
        $select->setFields('id,score');
        $grouping = $select->getGrouping();
        $grouping->addQueries([
            'cat:memory',
            'cat:no-such-cat',
        ]);
        $result = self::$client->select($select);
        $groupingComponentResult = $result->getComponent(ComponentAwareQueryInterface::COMPONENT_GROUPING);

        $queryGroup = $groupingComponentResult->getGroup('cat:memory');
        $this->assertSame(32, $queryGroup->getMatches());
        $this->assertSame(3, $queryGroup->getNumFound());
        $this->assertNotNull($queryGroup->getMaximumScore());

        $queryGroup = $groupingComponentResult->getGroup('cat:no-such-cat');
        $this->assertSame(32, $queryGroup->getMatches());
        $this->assertSame(0, $queryGroup->getNumFound());
        $this->assertNull($queryGroup->getMaximumScore());
    }

    public function testMoreLikeThisComponent()
    {
        $select = self::$client->createSelect();
        $select->setQuery('apache');
        $select->setSorts(['id' => SelectQuery::SORT_ASC]);

        $moreLikeThis = $select->getMoreLikeThis();
        $moreLikeThis->setFields('manu,cat');
        $moreLikeThis->setMinimumDocumentFrequency(1);
        $moreLikeThis->setMinimumTermFrequency(1);
        $moreLikeThis->setInterestingTerms('details');

        $result = self::$client->select($select);
        $this->assertSame(2, $result->getNumFound());

        $iterator = $result->getIterator();
        $mlt = $result->getMoreLikeThis();

        $document = $iterator->current();
        $this->assertSame('SOLR1000', $document->id);
        $mltResult = $mlt->getResult($document->id);
        // actual max. score isn't consistent across Solr 7 and 8, server and cloud
        // but it must always be a float
        $this->assertIsFloat($mltResult->getMaximumScore());
        $this->assertSame(1, $mltResult->getNumFound());
        $mltDoc = $mltResult->getIterator()->current();
        $this->assertSame('UTF8TEST', $mltDoc->id);

        $iterator->next();
        $document = $iterator->current();
        $this->assertSame('UTF8TEST', $document->id);
        $mltResult = $mlt->getResult($document->id);
        $this->assertIsFloat($mltResult->getMaximumScore());
        $this->assertSame(1, $mltResult->getNumFound());
        $mltDoc = $mltResult->getIterator()->current();
        $this->assertSame('SOLR1000', $mltDoc->id);

        // Solr 7 doesn't support mlt.interestingTerms for MoreLikeThisComponent
        // Solr 8 & Solr 9: "To use this parameter with the search component, the query cannot be distributed."
        // https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
        if (8 <= self::$solrVersion && $this instanceof AbstractServerTest) {
            // with 'details', interesting terms are an associative array of terms and their boost values
            $interestingTerms = $mlt->getInterestingTerm($document->id);
            $this->assertSame('cat:search', key($interestingTerms));
            $this->assertSame(1.0, current($interestingTerms));

            $moreLikeThis->setInterestingTerms('list');
            $result = self::$client->select($select);
            $document = $result->getIterator()->current();
            $mlt = $result->getMoreLikeThis();

            // with 'list', interesting terms are a numeric array of strings
            $interestingTerms = $mlt->getInterestingTerm($document->id);
            $this->assertSame(0, key($interestingTerms));
            $this->assertSame('cat:search', current($interestingTerms));

            $moreLikeThis->setInterestingTerms('none');
            $result = self::$client->select($select);
            $document = $result->getIterator()->current();
            $mlt = $result->getMoreLikeThis();

            // with 'none', interesting terms aren't available for the MLT result
            $this->expectException(UnexpectedValueException::class);
            $this->expectExceptionMessage('interestingterms is none');
            $mlt->getInterestingTerm($document->id);
        }
    }

    /**
     * There are a number of open issues that show MoreLikeThisHandler doesn't work as expected in SolrCloud mode.
     *
     * @see https://issues.apache.org/jira/browse/SOLR-4414
     * @see https://issues.apache.org/jira/browse/SOLR-5480
     *
     * @group skip_for_solr_cloud
     */
    public function testMoreLikeThisQuery()
    {
        $query = self::$client->createMoreLikethis();

        // the document we query to get similar documents for
        $query->setQuery('id:SP2514N');
        $query->setMltFields('manu,cat');
        $query->setMinimumDocumentFrequency(1);
        $query->setMinimumTermFrequency(1);
        $query->setInterestingTerms('details');
        // ensures we can consistently test for boost=1.0
        $query->setBoost(false);
        $query->setMatchInclude(true);
        $query->createFilterQuery('stock')->setQuery('inStock:true');

        $resultset = self::$client->moreLikeThis($query);
        $this->assertSame(7, $resultset->getNumFound());

        $iterator = $resultset->getIterator();
        $document = $iterator->current();
        $this->assertSame('6H500F0', $document->id);

        $matchDocument = $resultset->getMatch();
        $this->assertSame('SP2514N', $matchDocument->id);

        // with 'details', interesting terms are an associative array of terms and their boost values
        $interestingTerms = $resultset->getInterestingTerms();
        $this->assertSame('cat:electronics', key($interestingTerms));
        $this->assertSame(1.0, current($interestingTerms));

        $query->setInterestingTerms('list');
        $resultset = self::$client->moreLikeThis($query);

        // with 'list', interesting terms are a numeric array of strings
        $interestingTerms = $resultset->getInterestingTerms();
        $this->assertSame(0, key($interestingTerms));
        $this->assertSame('electronics', current($interestingTerms));

        $query->setInterestingTerms('none');
        $resultset = self::$client->moreLikeThis($query);

        // with 'none', interesting terms aren't available for the result set
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('interestingterms is none');
        $resultset->getInterestingTerms();
    }

    /**
     * There are a number of open issues that show MoreLikeThisHandler doesn't work as expected in SolrCloud mode.
     *
     * @see https://issues.apache.org/jira/browse/SOLR-4414
     * @see https://issues.apache.org/jira/browse/SOLR-5480
     *
     * @group skip_for_solr_cloud
     */
    public function testMoreLikeThisStream()
    {
        $query = self::$client->createMoreLikethis();

        // the supplied text we want similar documents for
        $text = <<<EOT
            Samsung SpinPoint P120 SP2514N - hard drive - 250 GB - ATA-133
            7200RPM, 8MB cache, IDE Ultra ATA-133, NoiseGuard, SilentSeek technology, Fluid Dynamic Bearing (FDB) motor
            EOT;

        $query->setQuery($text);
        $query->setQueryStream(true);
        $query->setMltFields('name,features');
        $query->setMinimumDocumentFrequency(1);
        $query->setMinimumTermFrequency(1);
        $query->setInterestingTerms('details');
        // ensures we can consistently test for boost=1.0
        $query->setBoost(false);
        $query->setMatchInclude(true);
        $query->createFilterQuery('stock')->setQuery('inStock:true');

        $resultset = self::$client->moreLikeThis($query);
        $this->assertSame(3, $resultset->getNumFound());

        $iterator = $resultset->getIterator();
        $document = $iterator->current();
        $this->assertSame('SP2514N', $document->id);

        // there is no match document to return, even with matchinclude true
        $this->assertNull($resultset->getMatch());

        // with 'details', interesting terms are an associative array of terms and their boost values
        $interestingTerms = $resultset->getInterestingTerms();
        // which term comes first differs between Solr 7 and 8, but it must always be a string
        $this->assertIsString(key($interestingTerms));
        $this->assertSame(1.0, current($interestingTerms));

        $query->setInterestingTerms('list');
        $resultset = self::$client->moreLikeThis($query);

        // with 'list', interesting terms are a numeric array of strings
        $interestingTerms = $resultset->getInterestingTerms();
        $this->assertSame(0, key($interestingTerms));
        $this->assertIsString(current($interestingTerms));

        $query->setInterestingTerms('none');
        $resultset = self::$client->moreLikeThis($query);

        // with 'none', interesting terms aren't available for the result set
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('interestingterms is none');
        $resultset->getInterestingTerms();
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
        // Some spellcheck dictionaries need to be built first, but not on every request!
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

        $spellcheck->setQuery('power cort cort');
        // Already built on the previous request.
        $spellcheck->setBuild(false);
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
        self::$client->registerQueryType('test', TermsTestQuery::class);
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
        $select->setFields('id,name,cat,weight');
        $update = self::$client->createUpdate();

        $doc = $update->createDocument();
        $doc->setField('id', 'solarium-test');
        $doc->setField('name', 'Solarium Test');
        $doc->setField('cat', 'solarium-test');
        $doc->setField('weight', 17.01);
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
            'weight' => 17.01,
        ], $result->getIterator()->current()->getFields());

        // set
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', 'modifier-set');
        $doc->setFieldModifier('cat', $doc::MODIFIER_SET);
        $doc->setField('weight', 42.0);
        $doc->setFieldModifier('weight', $doc::MODIFIER_SET);
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
            'weight' => 42.0,
        ], $result->getIterator()->current()->getFields());

        // add & inc
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', 'modifier-add');
        $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
        $doc->setField('weight', 5);
        $doc->setFieldModifier('weight', $doc::MODIFIER_INC);
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
            'weight' => 47.0,
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
            'weight' => 47.0,
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
            'weight' => 47.0,
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
            'weight' => 47.0,
        ], $result->getIterator()->current()->getFields());

        // remove & negative inc
        $doc = $update->createDocument();
        $doc->setKey('id', 'solarium-test');
        $doc->setField('cat', 'modifier-set');
        $doc->setFieldModifier('cat', $doc::MODIFIER_REMOVE);
        $doc->setField('weight', -5);
        $doc->setFieldModifier('weight', $doc::MODIFIER_INC);
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
            'weight' => 42.0,
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
            'weight' => 42.0,
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
            'weight' => 42.0,
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
            'weight' => 42.0,
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
        // cat comes after weight now because it was added later!
        $this->assertSame([
            'id' => 'solarium-test',
            'name' => 'Solarium Test',
            'weight' => 42.0,
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
            'weight' => 42.0,
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
            'single_child' => [
                'id' => 'solarium-single-child',
                'name' => 'Solarium Nested Document Single Child',
                'cat' => ['solarium-nested-document', 'child'],
                'price' => 0.0,
            ],
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
        $this->assertCount(6, $result);

        // without a sort, children are returned before their parents because they're added in that order to the underlying Lucene index
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-single-child',
            'name' => 'Solarium Nested Document Single Child',
            'price' => 0.0,
        ], $iterator->current()->getFields());
        $iterator->next();
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
            $select->setFields('id,single_child,children,grandchildren,[child]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                // labelled single nested child documents can't be indexed in XML (SOLR-16183)
                /*
                'single_child' => [
                    'id' => 'solarium-single-child',
                ],
                 */
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
            $select->setFields('id,single_child,price,children,grandchildren,[child childFilter=price:2.1]');
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
            $select->setFields('id,single_child,children,grandchildren,[child childFilter=/children/*:*]');
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
            $select->setFields('id,single_child,children,grandchildren,[child limit=2]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                // labelled single nested child documents can't be indexed in XML (SOLR-16183)
                /*
                'single_child' => [
                    'id' => 'solarium-single-child',
                ],
                 */
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
            $select->setFields('id,name,price,single_child,children,grandchildren,[child fl=id,price]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                'name' => 'Solarium Nested Document Parent',
                // labelled single nested child documents can't be indexed in XML (SOLR-16183)
                /*
                'single_child' => [
                    'id' => 'solarium-single-child',
                    'price' => 0.0,
                ],
                 */
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
        $select->setQuery('{!parent which="cat:parent"}id:solarium-single-child');
        $select->setFields('id');
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-parent',
        ], $iterator->current()->getFields());
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
        $this->assertCount(5, $result);
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-single-child',
        ], $iterator->current()->getFields());
        $iterator->next();
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
            // atomic update: replacing all child documents in a pseudo-field
            $newChildren = [
                [
                    'id' => 'solarium-child-3',
                    'name' => 'Solarium Nested Document Child 3',
                    'cat' => ['solarium-nested-document', 'child'],
                    'price' => 3.0,
                ],
                [
                    'id' => 'solarium-child-4',
                    'name' => 'Solarium Nested Document Child 4',
                    'cat' => ['solarium-nested-document', 'child'],
                    'price' => 4.0,
                ],
            ];
            $doc = $update->createDocument();
            $doc->setKey('id', 'solarium-parent');
            $doc->setField('cat', 'updated-1');
            $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
            $doc->setField('children', $newChildren);
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
                    'updated-1',
                ],
                'children' => [
                    [
                        'id' => 'solarium-child-3',
                        'name' => 'Solarium Nested Document Child 3',
                        'cat' => ['solarium-nested-document', 'child'],
                        'price' => 3.0,
                    ],
                    [
                        'id' => 'solarium-child-4',
                        'name' => 'Solarium Nested Document Child 4',
                        'cat' => ['solarium-nested-document', 'child'],
                        'price' => 4.0,
                    ],
                ],
            ], $iterator->current()->getFields());

            // atomic update: removing all child documents from a pseudo-field
            $doc = $update->createDocument();
            $doc->setKey('id', 'solarium-parent');
            $doc->setField('cat', 'updated-2');
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
                    'updated-1',
                    'updated-2',
                ],
            ], $iterator->current()->getFields());

            // other atomic updates (replacing, adding, removing individual child documents) can't be executed through XML (SOLR-12677)
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

    public function testUpdateWithoutControlCharacterFiltering()
    {
        $data = [
            'id' => 'solarium-unfiltered-control-chars',
            'name' => 'Solarium Document With Control Characters',
            'cat' => [
                'Backspace: '.chr(8),
                'Shift In: '.chr(15),
            ],
        ];

        $update = new NonControlCharFilteringUpdateQuery();
        $doc = $update->createDocument($data);
        $update->addDocument($doc);

        $this->expectException(HttpException::class);
        self::$client->update($update);
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
            $addDocument = function (BufferedAddAddDocumentEvent $event) use (&$document, &$weight) {
                $this->assertSame($document, $event->getDocument());
                $document->setField('weight', ++$weight);
            }
        );

        self::$client->getEventDispatcher()->addListener(
            BufferedAddEvents::PRE_FLUSH,
            $preFlush = function (BufferedAddPreFlushEvent $event) use ($bufferSize, &$document, &$weight) {
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
            $postFlush = function (BufferedAddPostFlushEvent $event) use ($bufferSize) {
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
            $preCommit = function (BufferedAddPreCommitEvent $event) use ($bufferSize, $totalDocs, &$document, &$weight) {
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
            $postCommit = function (BufferedAddPostCommitEvent $event) use ($bufferSize, $totalDocs) {
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
        self::$client->getEventDispatcher()->removeListener(BufferedAddEvents::ADD_DOCUMENT, $addDocument);
        self::$client->getEventDispatcher()->removeListener(BufferedAddEvents::PRE_FLUSH, $preFlush);
        self::$client->getEventDispatcher()->removeListener(BufferedAddEvents::POST_FLUSH, $postFlush);
        self::$client->getEventDispatcher()->removeListener(BufferedAddEvents::PRE_COMMIT, $preCommit);
        self::$client->getEventDispatcher()->removeListener(BufferedAddEvents::POST_COMMIT, $postCommit);
    }

    /**
     * @depends testBufferedAdd
     */
    public function testBufferedDelete()
    {
        $bufferSize = 3;

        $buffer = self::$client->getPlugin('buffereddelete');
        $buffer->setBufferSize($bufferSize);

        $id = '';
        $query = '';

        self::$client->getEventDispatcher()->addListener(
            BufferedDeleteEvents::ADD_DELETE_BY_ID,
            $addDeleteById = function (BufferedDeleteAddDeleteByIdEvent $event) use (&$id) {
                $this->assertSame($id, $event->getId());

                $event->setId('solarium-bufferedadd-'.$id);
            }
        );

        self::$client->getEventDispatcher()->addListener(
            BufferedDeleteEvents::ADD_DELETE_QUERY,
            $addDeleteQuery = function (BufferedDeleteAddDeleteQueryEvent $event) use (&$query) {
                $this->assertSame($query, $event->getQuery());

                // other documents in techproducts must never be deleted by this test
                $event->setQuery('cat:solarium-bufferedadd AND '.$query);
            }
        );

        $buffer->addDeleteQuery($query = 'cat:solarium-bufferedadd');
        $buffer->addDeleteById($id = 'preflush-1');
        $this->assertCount(2, $buffer->getDeletes());
        $buffer->clear();
        $this->assertCount(0, $buffer->getDeletes());

        for ($i = 1; $i <= 12; ++$i) {
            $buffer->addDeleteById($id = $i);
        }

        $buffer->addDeleteQuery($query = 'weight:[16 TO 26]');
        $buffer->addDeleteById($id = 'precommit-1');
        $buffer->commit(null, true, true);

        $select = self::$client->createSelect();
        $select->setQuery('cat:solarium-bufferedadd');
        $select->addSort('weight', $select::SORT_ASC);
        $select->setFields('id');
        $select->setRows(4);
        $result = self::$client->select($select);
        $this->assertSame(4, $result->getNumFound());

        $ids = [];
        /** @var \Solarium\QueryType\Select\Result\Document $document */
        foreach ($result as $document) {
            $ids[] = $document->id;
        }

        $this->assertEquals([
            'solarium-bufferedadd-preflush-1',
            'solarium-bufferedadd-13',
            'solarium-bufferedadd-24',
            'solarium-bufferedadd-25',
            ], $ids);

        // cleanup
        self::$client->getEventDispatcher()->removeListener(BufferedDeleteEvents::ADD_DELETE_BY_ID, $addDeleteById);
        self::$client->getEventDispatcher()->removeListener(BufferedDeleteEvents::ADD_DELETE_QUERY, $addDeleteQuery);
        $buffer->addDeleteQuery('cat:solarium-bufferedadd');
        $buffer->commit(null, true, true);
        $result = self::$client->select($select);
        $this->assertSame(0, $result->getNumFound());
    }

    public function testBufferedAddAndDelete()
    {
        $bufferSize = 10;

        $addBuffer = self::$client->getPlugin('bufferedadd');
        $addBuffer->setBufferSize($bufferSize);

        $delBuffer = self::$client->getPlugin('buffereddelete');

        $weight = 0;

        self::$client->getEventDispatcher()->addListener(
            BufferedAddEvents::ADD_DOCUMENT,
            $addDocument = function (BufferedAddAddDocumentEvent $event) use (&$weight) {
                $event->getDocument()->setField('weight', ++$weight);
            }
        );

        self::$client->getEventDispatcher()->addListener(
            BufferedDeleteEvents::ADD_DELETE_QUERY,
            $addDeleteQuery = function (BufferedDeleteAddDeleteQueryEvent $event) {
                // other documents in techproducts must never be deleted by this test
                $event->setQuery('cat:solarium-bufferedadd AND '.$event->getQuery());
            }
        );

        for ($i = 1; $i <= 15; ++$i) {
            $data = [
                'id' => 'solarium-bufferedadd-'.$i,
                'cat' => 'solarium-bufferedadd',
            ];
            $addBuffer->createDocument($data);
        }

        $addBuffer->flush();

        $delBuffer->addDeleteById('solarium-bufferedadd-8');
        $delBuffer->addDeleteById('solarium-bufferedadd-4');
        $delBuffer->flush();

        foreach (range('a', 'c') as $i) {
            $data = [
                'id' => 'solarium-bufferedadd-'.$i,
                'cat' => 'solarium-bufferedadd',
            ];
            $addBuffer->createDocument($data);
        }

        $addBuffer->flush();

        $delBuffer->addDeleteQuery('weight:[* TO 5]');
        $delBuffer->addDeleteById('solarium-bufferedadd-b');
        $delBuffer->flush();

        foreach (range('d', 'e') as $i) {
            $data = [
                'id' => 'solarium-bufferedadd-'.$i,
                'cat' => 'solarium-bufferedadd',
            ];
            $addBuffer->createDocument($data);
        }

        $addBuffer->flush();

        $delBuffer->addDeleteById('solarium-bufferedadd-d');
        $delBuffer->addDeleteById('solarium-bufferedadd-13');
        $delBuffer->flush();

        // either buffer can be committed as long as the other one has been flushed
        $addBuffer->commit(null, true, true);

        $select = self::$client->createSelect();
        $select->setQuery('cat:solarium-bufferedadd');
        $select->addSort('weight', $select::SORT_ASC);
        $select->setFields('id');
        $select->setRows(11);
        $result = self::$client->select($select);
        $this->assertSame(11, $result->getNumFound());

        $ids = [];
        /** @var \Solarium\QueryType\Select\Result\Document $document */
        foreach ($result as $document) {
            $ids[] = $document->id;
        }

        $this->assertEquals([
            'solarium-bufferedadd-6',
            'solarium-bufferedadd-7',
            'solarium-bufferedadd-9',
            'solarium-bufferedadd-10',
            'solarium-bufferedadd-11',
            'solarium-bufferedadd-12',
            'solarium-bufferedadd-14',
            'solarium-bufferedadd-15',
            'solarium-bufferedadd-a',
            'solarium-bufferedadd-c',
            'solarium-bufferedadd-e',
            ], $ids);

        // cleanup
        self::$client->getEventDispatcher()->removeListener(BufferedAddEvents::ADD_DOCUMENT, $addDocument);
        self::$client->getEventDispatcher()->removeListener(BufferedDeleteEvents::ADD_DELETE_QUERY, $addDeleteQuery);
        $delBuffer->addDeleteQuery('cat:solarium-bufferedadd');
        $delBuffer->commit(null, true, true);
        $result = self::$client->select($select);
        $this->assertSame(0, $result->getNumFound());
    }

    public function testBufferedAddLite(): int
    {
        $bufferSize = 10;
        $totalDocs = 25;

        $failListener = function (Event $event) {
            $this->fail(sprintf('BufferedAddLite isn\'t supposed to trigger %s', \get_class($event)));
        };

        self::$client->getEventDispatcher()->addListener(BufferedAddEvents::ADD_DOCUMENT, $failListener);
        self::$client->getEventDispatcher()->addListener(BufferedAddEvents::PRE_FLUSH, $failListener);
        self::$client->getEventDispatcher()->addListener(BufferedAddEvents::POST_FLUSH, $failListener);
        self::$client->getEventDispatcher()->addListener(BufferedAddEvents::PRE_COMMIT, $failListener);
        self::$client->getEventDispatcher()->addListener(BufferedAddEvents::POST_COMMIT, $failListener);

        $buffer = self::$client->getPlugin('bufferedaddlite');
        $buffer->setBufferSize($bufferSize);

        $update = self::$client->createUpdate();
        for ($i = 1; $i <= $totalDocs; ++$i) {
            $data = [
                'id' => 'solarium-bufferedaddlite-'.$i,
                'cat' => 'solarium-bufferedaddlite',
                'weight' => $i,
            ];
            $document = $update->createDocument($data);
            $buffer->addDocument($document);
        }

        $buffer->commit(null, true, true);

        $select = self::$client->createSelect();
        $select->setQuery('cat:solarium-bufferedaddlite');
        $select->addSort('weight', $select::SORT_ASC);
        $select->setFields('id');
        $select->setRows($totalDocs);
        $result = self::$client->select($select);
        $this->assertSame($totalDocs, $result->getNumFound());

        $ids = [];
        /** @var \Solarium\QueryType\Select\Result\Document $document */
        foreach ($result as $document) {
            $ids[] = $document->id;
        }

        $this->assertEquals([
            'solarium-bufferedaddlite-1',
            'solarium-bufferedaddlite-2',
            'solarium-bufferedaddlite-3',
            'solarium-bufferedaddlite-4',
            'solarium-bufferedaddlite-5',
            'solarium-bufferedaddlite-6',
            'solarium-bufferedaddlite-7',
            'solarium-bufferedaddlite-8',
            'solarium-bufferedaddlite-9',
            'solarium-bufferedaddlite-10',
            'solarium-bufferedaddlite-11',
            'solarium-bufferedaddlite-12',
            'solarium-bufferedaddlite-13',
            'solarium-bufferedaddlite-14',
            'solarium-bufferedaddlite-15',
            'solarium-bufferedaddlite-16',
            'solarium-bufferedaddlite-17',
            'solarium-bufferedaddlite-18',
            'solarium-bufferedaddlite-19',
            'solarium-bufferedaddlite-20',
            'solarium-bufferedaddlite-21',
            'solarium-bufferedaddlite-22',
            'solarium-bufferedaddlite-23',
            'solarium-bufferedaddlite-24',
            'solarium-bufferedaddlite-25',
            ], $ids);

        // cleanup
        self::$client->getEventDispatcher()->removeListener(BufferedAddEvents::ADD_DOCUMENT, $failListener);
        self::$client->getEventDispatcher()->removeListener(BufferedAddEvents::PRE_FLUSH, $failListener);
        self::$client->getEventDispatcher()->removeListener(BufferedAddEvents::POST_FLUSH, $failListener);
        self::$client->getEventDispatcher()->removeListener(BufferedAddEvents::PRE_COMMIT, $failListener);
        self::$client->getEventDispatcher()->removeListener(BufferedAddEvents::POST_COMMIT, $failListener);

        return $totalDocs;
    }

    /**
     * @depends testBufferedAddLite
     *
     * @param int $totalDocs
     */
    public function testBufferedDeleteLite(int $totalDocs)
    {
        $bufferSize = 20;

        $failListener = function (Event $event) {
            $this->fail(sprintf('BufferedDeleteLite isn\'t supposed to trigger %s', \get_class($event)));
        };

        self::$client->getEventDispatcher()->addListener(BufferedDeleteEvents::ADD_DELETE_BY_ID, $failListener);
        self::$client->getEventDispatcher()->addListener(BufferedDeleteEvents::ADD_DELETE_QUERY, $failListener);
        self::$client->getEventDispatcher()->addListener(BufferedDeleteEvents::PRE_FLUSH, $failListener);
        self::$client->getEventDispatcher()->addListener(BufferedDeleteEvents::POST_FLUSH, $failListener);
        self::$client->getEventDispatcher()->addListener(BufferedDeleteEvents::PRE_COMMIT, $failListener);
        self::$client->getEventDispatcher()->addListener(BufferedDeleteEvents::POST_COMMIT, $failListener);

        $buffer = self::$client->getPlugin('buffereddeletelite');
        $buffer->setBufferSize($bufferSize);

        for ($i = 1; $i <= $totalDocs; ++$i) {
            $buffer->addDeleteById('solarium-bufferedaddlite-'.$i);
        }

        // doesn't match anything, just making sure it doesn't trigger an event
        $buffer->addDeleteQuery('cat:solarium-buffereddeletelite');

        $buffer->commit(null, true, true);

        $select = self::$client->createSelect();
        $select->setQuery('cat:solarium-bufferedaddlite');
        $result = self::$client->select($select);
        $this->assertSame(0, $result->getNumFound());

        // cleanup
        self::$client->getEventDispatcher()->removeListener(BufferedDeleteEvents::ADD_DELETE_BY_ID, $failListener);
        self::$client->getEventDispatcher()->removeListener(BufferedDeleteEvents::ADD_DELETE_QUERY, $failListener);
        self::$client->getEventDispatcher()->removeListener(BufferedDeleteEvents::PRE_FLUSH, $failListener);
        self::$client->getEventDispatcher()->removeListener(BufferedDeleteEvents::POST_FLUSH, $failListener);
        self::$client->getEventDispatcher()->removeListener(BufferedDeleteEvents::PRE_COMMIT, $failListener);
        self::$client->getEventDispatcher()->removeListener(BufferedDeleteEvents::POST_COMMIT, $failListener);
    }

    public function testPrefetchIterator()
    {
        $select = self::$client->createSelect();
        $select->addSort('id', SelectQuery::SORT_ASC);
        /** @var PrefetchIterator $prefetch */
        $prefetch = self::$client->getPlugin('prefetchiterator');
        $prefetch->setPrefetch(2);
        $prefetch->setQuery($select);

        // check upfront that all results are found
        $this->assertCount(32, $prefetch);

        // verify that each result is iterated in order
        $id = '';
        for ($i = 0; $prefetch->valid(); $prefetch->next(), ++$i) {
            $this->assertLessThan(0, strcmp($id, $id = $prefetch->current()->id));
        }
        $this->assertSame(32, $i);
    }

    public function testPrefetchIteratorWithCursorMark()
    {
        $select = self::$client->createSelect();
        $select->setCursorMark('*');
        $select->addSort('id', SelectQuery::SORT_ASC);
        /** @var PrefetchIterator $prefetch */
        $prefetch = self::$client->getPlugin('prefetchiterator');
        $prefetch->setPrefetch(2);
        $prefetch->setQuery($select);

        // check upfront that all results are found
        $this->assertCount(32, $prefetch);

        // verify that each result is iterated in order
        $id = '';
        for ($i = 0; $prefetch->valid(); $prefetch->next(), ++$i) {
            $this->assertLessThan(0, strcmp($id, $id = $prefetch->current()->id));
        }
        $this->assertSame(32, $i);
    }

    public function testPrefetchIteratorWithoutAndWithCursorMark()
    {
        $select = self::$client->createSelect();
        $select->addSort('id', SelectQuery::SORT_ASC);
        /** @var PrefetchIterator $prefetch */
        $prefetch = self::$client->getPlugin('prefetchiterator');
        $prefetch->setPrefetch(2);
        $prefetch->setQuery($select);

        $without = [];
        foreach ($prefetch as $document) {
            $without = $document->id;
        }

        $select = self::$client->createSelect();
        $select->setCursorMark('*');
        $select->addSort('id', SelectQuery::SORT_ASC);
        $prefetch->setQuery($select);

        $with = [];
        foreach ($prefetch as $document) {
            $with = $document->id;
        }

        $this->assertSame($without, $with);
    }

    public function testPrefetchIteratorManualRewind()
    {
        $select = self::$client->createSelect();
        $select->addSort('id', SelectQuery::SORT_ASC);
        /** @var PrefetchIterator $prefetch */
        $prefetch = self::$client->getPlugin('prefetchiterator');
        $prefetch->setPrefetch(5);
        $prefetch->setQuery($select);

        // check if valid (this will fetch the first set of documents)
        $this->assertTrue($prefetch->valid());
        // check that we're at position 0
        $this->assertSame(0, $prefetch->key());
        // current document is the one with lowest alphabetical id in techproducts
        $this->assertSame('0579B002', $prefetch->current()->id);

        // move to an arbitrary point past the first set of fetched documents
        while (12 > $prefetch->key()) {
            $prefetch->next();
            // this ensures the next set will be fetched when we've passed the end of a set
            $this->assertTrue($prefetch->valid());
        }

        // check that we've reached the expected document at position 12
        $this->assertSame(12, $prefetch->key());
        $this->assertSame('NOK', $prefetch->current()->id);

        // this resets the position and clears the last fetched result
        $prefetch->rewind();

        // check if valid (this will re-fetch the first set of documents)
        $this->assertTrue($prefetch->valid());
        // check that we're back at position 0
        $this->assertSame(0, $prefetch->key());
        // current document is once again the one with lowest alphabetical id in techproducts
        $this->assertSame('0579B002', $prefetch->current()->id);
    }

    /**
     * @testWith [false]
     *           [true]
     */
    public function testExtractIntoDocument(bool $usePostBigExtractRequestPlugin)
    {
        if ($usePostBigExtractRequestPlugin) {
            $postBigExtractRequest = self::$client->getPlugin('postbigextractrequest');
            // make sure the GET parameters will be converted to POST
            $postBigExtractRequest->setMaxQueryStringLength(1);

            $this->assertArrayHasKey('postbigextractrequest', self::$client->getPlugins());
        } else {
            $this->assertArrayNotHasKey('postbigextractrequest', self::$client->getPlugins());
        }

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
        $this->assertMatchesRegularExpression('/^HTML Test Title\s+HTML Test Body$/', trim($document['content'][0]), 'Written document does not contain extracted result');

        // now cleanup the documents to have the initial index state
        $update = self::$client->createUpdate();
        $update->addDeleteQuery('cat:extract-test');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);

        if ($usePostBigExtractRequestPlugin) {
            self::$client->removePlugin('postbigextractrequest');
            $this->assertArrayNotHasKey('postbigextractrequest', self::$client->getPlugins());
        }
    }

    /**
     * @testWith [false]
     *           [true]
     */
    public function testExtractOnlyText(bool $usePostBigExtractRequestPlugin)
    {
        if ($usePostBigExtractRequestPlugin) {
            $postBigExtractRequest = self::$client->getPlugin('postbigextractrequest');
            // make sure the GET parameters will be converted to POST
            $postBigExtractRequest->setMaxQueryStringLength(1);

            $this->assertArrayHasKey('postbigextractrequest', self::$client->getPlugins());
        } else {
            $this->assertArrayNotHasKey('postbigextractrequest', self::$client->getPlugins());
        }

        $query = self::$client->createExtract();
        $query->setExtractOnly(true);
        $query->setExtractFormat($query::EXTRACT_FORMAT_TEXT);
        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testpdf.pdf');

        $response = self::$client->extract($query);
        $this->assertSame('PDF Test', trim($response->getData()['testpdf.pdf']), 'Can not extract the plain content from the PDF file');
        $this->assertSame('PDF Test', trim($response->getData()['file']), 'Can not extract the plain content from the PDF file');

        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testhtml.html');

        $response = self::$client->extract($query);
        $this->assertMatchesRegularExpression('/^HTML Test Title\s+HTML Test Body$/', trim($response->getData()['testhtml.html']), 'Can not extract the plain content from the HTML file');
        $this->assertMatchesRegularExpression('/^HTML Test Title\s+HTML Test Body$/', trim($response->getData()['file']), 'Can not extract the plain content from the HTML file');

        if ($usePostBigExtractRequestPlugin) {
            self::$client->removePlugin('postbigextractrequest');
            $this->assertArrayNotHasKey('postbigextractrequest', self::$client->getPlugins());
        }
    }

    /**
     * @testWith [false]
     *           [true]
     */
    public function testExtractOnlyXml(bool $usePostBigExtractRequestPlugin)
    {
        if ($usePostBigExtractRequestPlugin) {
            $postBigExtractRequest = self::$client->getPlugin('postbigextractrequest');
            // make sure the GET parameters will be converted to POST
            $postBigExtractRequest->setMaxQueryStringLength(1);

            $this->assertArrayHasKey('postbigextractrequest', self::$client->getPlugins());
        } else {
            $this->assertArrayNotHasKey('postbigextractrequest', self::$client->getPlugins());
        }

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

        if ($usePostBigExtractRequestPlugin) {
            self::$client->removePlugin('postbigextractrequest');
            $this->assertArrayNotHasKey('postbigextractrequest', self::$client->getPlugins());
        }
    }

    /**
     * Test different input encodings for Extract queries.
     *
     * This method tests two technically distinct applications of encoding.
     *
     * The encoding set on the query tells Solr how the query parameters are
     * encoded. It doesn't mean anything for the content of the file.
     *
     * The encoding of the file content is detected by Tika and isn't influenced
     * by the encoding of the query parameters.
     *
     * @testWith [false]
     *           [true]
     */
    public function testExtractInputEncoding(bool $usePostBigExtractRequestPlugin)
    {
        if ($usePostBigExtractRequestPlugin) {
            $postBigExtractRequest = self::$client->getPlugin('postbigextractrequest');
            // make sure the GET parameters will be converted to POST
            $postBigExtractRequest->setMaxQueryStringLength(1);

            $this->assertArrayHasKey('postbigextractrequest', self::$client->getPlugins());
        } else {
            $this->assertArrayNotHasKey('postbigextractrequest', self::$client->getPlugins());
        }

        $extract = self::$client->createExtract();
        $extract->setUprefix('attr_');
        $extract->setCommit(true);
        $extract->setCommitWithin(0);

        $extract->setInputEncoding('us-ascii');
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test us-ascii !#$%&\'()+,-.;=@[]^_`{}~.txt');

        $doc = $extract->createDocument();
        $doc->id = iconv('UTF-8', 'US-ASCII', 'extract-ie-test-1-us-ascii');
        $doc->cat = [iconv('UTF-8', 'US-ASCII', 'extract-ie-test')];
        $doc->name = iconv('UTF-8', 'US-ASCII', 'test us-ascii !#$%&\'()+,-.;=@[]^_`{}~');
        $extract->setDocument($doc);
        self::$client->extract($extract);

        $extract->setInputEncoding('utf-8');
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test utf-8 αβγ абв אԱა.txt');

        $doc = $extract->createDocument();
        $doc->id = 'extract-ie-test-2-utf-8';
        $doc->cat = ['extract-ie-test'];
        $doc->name = 'test utf-8 αβγ абв אԱა';
        $extract->setDocument($doc);
        self::$client->extract($extract);

        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test utf-8 fəˈnɛtık.txt');

        $doc = $extract->createDocument();
        $doc->id = 'extract-ie-test-3-utf-8-phonetic';
        $doc->cat = ['extract-ie-test'];
        $doc->name = 'test utf-8 fəˈnɛtık';
        $extract->setDocument($doc);
        self::$client->extract($extract);

        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test utf-8 コンニチハ.txt');

        $doc = $extract->createDocument();
        $doc->id = 'extract-ie-test-4-utf-8-katakana';
        $doc->cat = ['extract-ie-test'];
        $doc->name = 'test utf-8 コンニチハ';
        $extract->setDocument($doc);
        self::$client->extract($extract);

        $extract->setInputEncoding('iso-8859-1');
        // test with a file that specifies the encoding, Tika has a hard time telling ISO-8859-* and Windows-* sets apart on plain text with this little data
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test iso-8859-1 ¡¢£¤¥¦§¨©ª«¬.xml');

        $doc = $extract->createDocument();
        $doc->id = iconv('UTF-8', 'ISO-8859-1', 'extract-ie-test-5-iso-8859-1');
        $doc->cat = [iconv('UTF-8', 'ISO-8859-1', 'extract-ie-test')];
        $doc->name = iconv('UTF-8', 'ISO-8859-1', 'test iso-8859-1 ¡¢£¤¥¦§¨©ª«¬');
        $extract->setDocument($doc);
        self::$client->extract($extract);

        $extract->setInputEncoding('gb18030');
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test gb18030 这份文件是很有光泽.txt');

        $doc = $extract->createDocument();
        $doc->id = iconv('UTF-8', 'GB18030', 'extract-ie-test-6-gb18030');
        $doc->cat = [iconv('UTF-8', 'GB18030', 'extract-ie-test')];
        $doc->name = iconv('UTF-8', 'GB18030', 'test gb18030 这份文件是很有光泽');
        $extract->setDocument($doc);
        self::$client->extract($extract);

        // now get the documents and check the contents
        $select = self::$client->createSelect();
        $select->setQuery('cat:extract-ie-test');
        $select->addSort('id', $select::SORT_ASC);
        $selectResult = self::$client->select($select);
        $this->assertCount(6, $selectResult);
        $iterator = $selectResult->getIterator();

        /** @var Document $document */
        $document = $iterator->current();
        $this->assertSame('test us-ascii !#$%&\'()+,-.;=@[]^_`{}~', $document->name);
        // the file contains all 128 codepoints of the full 7-bit US-ASCII table, but we only test for printable characters
        $printableASCII = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~';
        $this->assertStringContainsString($printableASCII, $document['content'][0], 'Could not extract from file with US-ASCII characters');

        $iterator->next();
        $document = $iterator->current();
        $this->assertSame('test utf-8 αβγ абв אԱა', $document->name);
        // the file contains some example text from https://www.w3.org/2001/06/utf-8-test/UTF-8-demo.html
        $sampleUTF8 = '£©µÀÆÖÞßéöÿ ΑΒΓΔΩαβγδω АБВГДабвгд ﬁ�⑀₂ἠḂӥẄɐː⍎אԱა';
        $this->assertSame($sampleUTF8, trim($document['content'][0]), 'Could not extract from file with UTF-8 characters');

        $iterator->next();
        $document = $iterator->current();
        $this->assertSame('test utf-8 fəˈnɛtık', $document->name);
        // the file contains a phonetic example from https://www.w3.org/2001/06/utf-8-test/UTF-8-demo.html
        $samplePhonetic = 'ði ıntəˈnæʃənəl fəˈnɛtık əsoʊsiˈeıʃn';
        $this->assertSame($samplePhonetic, trim($document['content'][0]), 'Could not extract from file with UTF-8 phonetic characters');

        $iterator->next();
        $document = $iterator->current();
        $this->assertSame('test utf-8 コンニチハ', $document->name);
        // the file contains a Katakana example from https://www.w3.org/2001/06/utf-8-test/UTF-8-demo.html
        $sampleKatakana = 'コンニチハ';
        $this->assertSame($sampleKatakana, trim($document['content'][0]), 'Could not extract from file with UTF-8 Katakana characters');

        $iterator->next();
        $document = $iterator->current();
        $this->assertSame('test iso-8859-1 ¡¢£¤¥¦§¨©ª«¬', $document->name);
        // the file contains the printable characters from ISO-8859-1
        $printableISO88591 = ' ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ';
        $this->assertSame($printableISO88591, trim($document['content'][0]), 'Could not extract from file with ISO-8859-1 characters');

        $iterator->next();
        $document = $iterator->current();
        $this->assertSame('test gb18030 这份文件是很有光泽', $document->name);
        // the file contains a GB18030 example from the techproducts sample set
        $sampleGB18030 = '这份文件是很有光泽';
        $this->assertSame($sampleGB18030, trim($document['content'][0]), 'Could not extract from file with GB18030 characters');

        // now cleanup the documents to have the initial index state
        $update = self::$client->createUpdate();
        $update->addDeleteQuery('cat:extract-ie-test');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);

        if ($usePostBigExtractRequestPlugin) {
            self::$client->removePlugin('postbigextractrequest');
            $this->assertArrayNotHasKey('postbigextractrequest', self::$client->getPlugins());
        }
    }

    /**
     * @testWith [false]
     *           [true]
     */
    public function testExtractInvalidFile(bool $usePostBigExtractRequestPlugin)
    {
        if ($usePostBigExtractRequestPlugin) {
            $postBigExtractRequest = self::$client->getPlugin('postbigextractrequest');
            // make sure the GET parameters will be converted to POST
            $postBigExtractRequest->setMaxQueryStringLength(1);

            $this->assertArrayHasKey('postbigextractrequest', self::$client->getPlugins());
        } else {
            $this->assertArrayNotHasKey('postbigextractrequest', self::$client->getPlugins());
        }

        $extract = self::$client->createExtract();
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'nosuchfile');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Extract query file path/url invalid or not available: '.__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'nosuchfile');
        self::$client->extract($extract);

        if ($usePostBigExtractRequestPlugin) {
            self::$client->removePlugin('postbigextractrequest');
            $this->assertArrayNotHasKey('postbigextractrequest', self::$client->getPlugins());
        }
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

    /**
     * Get the options to use for ManagedResources Exists commands.
     *
     * @return array
     */
    public function getManagedResourcesExistsCommandOptions(): array
    {
        // Solr 7 can use HEAD requests because it's unaffected by SOLR-15116 and SOLR-16274
        if (7 === self::$solrVersion) {
            return [
                'useHeadRequest' => true,
            ];
        }

        return [];
    }

    public function testManagedStopwords()
    {
        /** @var StopwordsQuery $query */
        $query = self::$client->createManagedStopwords();
        $query->setName('english');
        $term = 'managed_stopword_test';

        // Check that stopword list exists
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Add stopwords
        $add = $query->createCommand($query::COMMAND_ADD);
        $add->setStopwords([$term]);
        $query->setCommand($add);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that added stopword exists
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $exists->setTerm($term);
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // We need to remove the current command in order to have no command. Having no command lists the items.
        $query->removeCommand();

        // List stopwords
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());
        $this->assertContains('managed_stopword_test', $result->getItems());

        // List added stopword only
        $query->setTerm($term);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());
        $this->assertSame(['managed_stopword_test'], $result->getItems());

        // Delete added stopword
        $delete = $query->createCommand($query::COMMAND_DELETE);
        $delete->setTerm($term);
        $query->setCommand($delete);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that added stopword is gone
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $exists->setTerm($term);
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());

        // List no longer added stopword
        $query->setTerm($term);
        $query->removeCommand();
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());
        $this->assertSame([], $result->getItems());
    }

    /**
     * @testWith ["testlist", "managed_stopword_test"]
     *           ["list res-chars :/?#[]@%", "term res-chars :?#[]@%"]
     */
    public function testManagedStopwordsCreation(string $name, string $term)
    {
        // don't use invalid filename characters in list name on Windows to avoid running into SOLR-15895
        if (self::$isSolrOnWindows) {
            $name = str_replace([':', '/', '?'], '', $name);
        }

        /** @var StopwordsQuery $query */
        $query = self::$client->createManagedStopwords();
        $query->setName($name.uniqid());

        // Check that stopword list doesn't exist
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());

        // Create a new stopword list
        $create = $query->createCommand($query::COMMAND_CREATE);
        $query->setCommand($create);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Configure the new list to be case sensitive
        $initArgs = $query->createInitArgs();
        $initArgs->setIgnoreCase(false);
        $config = $query->createCommand($query::COMMAND_CONFIG);
        $config->setInitArgs($initArgs);
        $query->setCommand($config);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that stopword list was created
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check the configuration
        $query->removeCommand();
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());
        $this->assertFalse($result->isIgnoreCase());

        // Check that we can add to it
        $add = $query->createCommand($query::COMMAND_ADD);
        $add->setStopwords([$term]);
        $query->setCommand($add);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that added stopword exists in its original lowercase form
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $exists->setTerm($term);
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that added stopword DOESN'T exist in uppercase form
        $exists->setTerm(strtoupper($term));
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());

        // Remove the stopword list
        $remove = $query->createCommand($query::COMMAND_REMOVE);
        $query->setCommand($remove);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that stopword list is gone
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());

        // Check that list can no longer be listed
        $query->removeCommand();
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());
        $this->assertSame([], $result->getItems());
    }

    public function testManagedSynonyms()
    {
        /** @var SynonymsQuery $query */
        $query = self::$client->createManagedSynonyms();
        $query->setName('english');
        $term = 'managed_synonyms_test';

        // Check that synonym map exists
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Add synonym mapping
        $add = $query->createCommand($query::COMMAND_ADD);
        $synonyms = new Synonyms();
        $synonyms->setTerm($term);
        $synonyms->setSynonyms(['managed_synonym', 'synonym_test']);
        $add->setSynonyms($synonyms);
        $query->setCommand($add);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that added synonym mapping exsists
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $exists->setTerm($term);
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // We need to remove the current command in order to have no command. Having no command lists the items.
        $query->removeCommand();

        // List synonyms
        $result = self::$client->execute($query);
        $items = $result->getItems();
        $this->assertTrue($result->getWasSuccessful());
        $success = false;
        /** @var SynonymsResultItem $item */
        foreach ($items as $item) {
            if ('managed_synonyms_test' === $item->getTerm()) {
                $success = true;
                $this->assertSame(['managed_synonym', 'synonym_test'], $item->getSynonyms());
            }
        }
        if (!$success) {
            $this->fail('Couldn\'t find synonym.');
        }

        // List added synonym mapping only
        $query->setTerm($term);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());
        $this->assertEquals(
            [new SynonymsResultItem('managed_synonyms_test', ['managed_synonym', 'synonym_test'])],
            $result->getItems()
        );

        // Delete added synonym mapping
        $delete = $query->createCommand($query::COMMAND_DELETE);
        $delete->setTerm($term);
        $query->setCommand($delete);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that added synonym mapping is gone
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $exists->setTerm($term);
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());

        // List no longer added synonym mapping
        $query->setTerm($term);
        $query->removeCommand();
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());
        $this->assertSame([], $result->getItems());
    }

    /**
     * @testWith ["testmap", "managed_synonyms_test"]
     *           ["map res-chars :/?#[]@%", "term res-chars :?#[]@%"]
     */
    public function testManagedSynonymsCreation(string $name, string $term)
    {
        // don't use invalid filename characters in map name on Windows to avoid running into SOLR-15895
        if (self::$isSolrOnWindows) {
            $name = str_replace([':', '/', '?'], '', $name);
        }

        /** @var SynonymsQuery $query */
        $query = self::$client->createManagedSynonyms();
        $query->setName($name.uniqid());

        // Check that synonym map doesn't exist
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());

        // Create a new synonym map
        $create = $query->createCommand($query::COMMAND_CREATE);
        $query->setCommand($create);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Configure the new map to be case sensitive and use the 'solr' format
        $initArgs = $query->createInitArgs();
        $initArgs->setIgnoreCase(false);
        $initArgs->setFormat($initArgs::FORMAT_SOLR);
        $config = $query->createCommand($query::COMMAND_CONFIG);
        $config->setInitArgs($initArgs);
        $query->setCommand($config);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that synonym map was created
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check the configuration
        $query->removeCommand();
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());
        $this->assertFalse($result->isIgnoreCase());
        $this->assertEquals($initArgs::FORMAT_SOLR, $result->getFormat());

        // Check that we can add to it
        $add = $query->createCommand($query::COMMAND_ADD);
        $synonyms = new Synonyms();
        $synonyms->setTerm($term);
        $synonyms->setSynonyms(['managed_synonym', 'synonym_test']);
        $add->setSynonyms($synonyms);
        $query->setCommand($add);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that synonym exists in its original lowercase form
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $exists->setTerm($term);
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that synonym DOESN'T exist in uppercase form
        $exists->setTerm(strtoupper($term));
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());

        // Remove the synonym map
        $remove = $query->createCommand($query::COMMAND_REMOVE);
        $query->setCommand($remove);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Check that synonym map is gone
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());

        // Check that map can no longer be listed
        $query->removeCommand();
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());
        $this->assertSame([], $result->getItems());
    }

    public function testManagedResources()
    {
        $query = self::$client->createManagedResources();
        $result = self::$client->execute($query);
        $items = $result->getItems();

        /*
         * Solr can also return resources that were created in previous tests, even if they were actually successfully removed.
         * Those resources never had any registered observers. We can use this fact to filter out the two default resources that
         * come with techproducts and check only those. We tally them because we can't count on numFound with the surplus resources.
         */

        $n = 0;
        /** @var ResourceResultItem $item */
        foreach ($items as $item) {
            if (0 !== $item->getNumObservers()) {
                ++$n;
                switch ($item->getType()) {
                    case ResourceResultItem::TYPE_STOPWORDS:
                        $this->assertSame('/schema/analysis/stopwords/english', $item->getResourceId());
                        $this->assertSame('org.apache.solr.rest.schema.analysis.ManagedWordSetResource', $item->getClass());
                        break;
                    case ResourceResultItem::TYPE_SYNONYMS:
                        $this->assertSame('/schema/analysis/synonyms/english', $item->getResourceId());
                        $this->assertSame('org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager', $item->getClass());
                        break;
                }
            }
        }

        // both resources must be present in the results
        $this->assertSame(2, $n);
    }

    /**
     * Compare our fix for Solr requiring special characters be doubly percent-encoded
     * with an RFC 3986 compliant implementation that uses single percent-encoding.
     *
     * If this test fails, Solr has probably fixed SOLR-6853 on their side. If that is
     * the case, we'll have to re-evaluate what to do about the workaround. As long as
     * no other tests fail, they're still supporting the workaround for BC.
     *
     * @see https://issues.apache.org/jira/browse/SOLR-6853
     * @see https://github.com/solariumphp/solarium/pull/742
     *
     * @dataProvider managedResourcesSolr6853Provider
     */
    public function testManagedResourcesSolr6853(string $resourceType, AbstractManagedResourceQuery $query, AbstractAddCommand $add)
    {
        // unique name is necessary for Stopwords to avoid running into SOLR-14268
        $uniqid = uniqid();

        // don't use invalid filename characters in resource name on Windows to avoid running into SOLR-15895
        if (self::$isSolrOnWindows) {
            $name = $uniqid.'test-#[]@% ';
            $nameSingleEncoded = $uniqid.'test-%23%5B%5D%40%25%20';
            $nameDoubleEncoded = $uniqid.'test-%2523%255B%255D%2540%2525%2520';
        } else {
            $name = $uniqid.'test-:/?#[]@% ';
            $nameSingleEncoded = $uniqid.'test-%3A%2F%3F%23%5B%5D%40%25%20';
            $nameDoubleEncoded = $uniqid.'test-%253A%252F%253F%2523%255B%255D%2540%2525%2520';
        }

        // unlike name, term can't contain a slash (SOLR-6853)
        $term = 'test-:?#[]@% ';
        $termSingleEncoded = 'test-%3A%3F%23%5B%5D%40%25%20';
        $termDoubleEncoded = 'test-%253A%253F%2523%255B%255D%2540%2525%2520';

        $compliantRequestBuilder = new CompliantManagedResourceRequestBuilder();
        $actualRequestBuilder = new ResourceRequestBuilder();

        $query->setName($name);

        // Create a new managed resource with reserved characters in the name
        $create = $query->createCommand($query::COMMAND_CREATE);
        $query->setCommand($create);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        $query->removeCommand();

        // Getting the resource with a compliant request builder doesn't work
        $request = $compliantRequestBuilder->build($query);
        $this->assertStringEndsWith('/'.$nameSingleEncoded, $request->getHandler());
        $response = self::$client->executeRequest($request);
        $result = self::$client->createResult($query, $response);
        $this->assertFalse($result->getWasSuccessful(), 'Check if SOLR-6853 is fixed.');

        // Since Solr 8.7, this returns an error message in JSON, earlier versions return an HTML page
        $expectedErrorMsg = sprintf('No REST managed resource registered for path /schema/analysis/%s/%stest-', $resourceType, $uniqid);
        if (8 <= self::$solrVersion) {
            $this->assertSame($expectedErrorMsg, json_decode($response->getBody())->error->msg, 'Check if SOLR-6853 is fixed.');
        } else {
            $this->assertStringContainsString('<p>'.$expectedErrorMsg.'</p>', $response->getBody(), 'Check if SOLR-6853 is fixed.');
        }

        // Getting the resource with our actual request builder does work
        $request = $actualRequestBuilder->build($query);
        $this->assertStringEndsWith('/'.$nameDoubleEncoded, $request->getHandler());
        $response = self::$client->executeRequest($request);
        $result = self::$client->createResult($query, $response);
        $this->assertTrue($result->getWasSuccessful());

        // Removing the resource with a compliant request builder doesn't work
        $remove = $query->createCommand($query::COMMAND_REMOVE);
        $query->setCommand($remove);
        $request = $compliantRequestBuilder->build($query);
        $response = self::$client->executeRequest($request);
        $result = self::$client->createResult($query, $response);
        $this->assertFalse($result->getWasSuccessful(), 'Check if SOLR-6853 is fixed.');

        // The resource still exists
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Removing the resource with our actual request builder does work
        $query->setCommand($remove);
        $request = $actualRequestBuilder->build($query);
        $response = self::$client->executeRequest($request);
        $result = self::$client->createResult($query, $response);
        $this->assertTrue($result->getWasSuccessful());

        // The resource is gone
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());

        // Add a term with reserved characters to a resource
        $query->setName('english');
        $query->setCommand($add);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        $query->removeCommand()->setTerm($term);

        // Getting the term with a compliant request builder doesn't work
        $request = $compliantRequestBuilder->build($query);
        $this->assertStringEndsWith('/english/'.$termSingleEncoded, $request->getHandler());
        $response = self::$client->executeRequest($request);
        $result = self::$client->createResult($query, $response);
        $this->assertFalse($result->getWasSuccessful(), 'Check if SOLR-6853 is fixed.');

        $expectedErrorMsg = sprintf('test- not found in /schema/analysis/%s/english', $resourceType);
        $this->assertSame($expectedErrorMsg, json_decode($response->getBody())->error->msg, 'Check if SOLR-6853 is fixed.');

        // Getting the term with our actual request builder does work
        $request = $actualRequestBuilder->build($query);
        $this->assertStringEndsWith('/english/'.$termDoubleEncoded, $request->getHandler());
        $response = self::$client->executeRequest($request);
        $result = self::$client->createResult($query, $response);
        $this->assertTrue($result->getWasSuccessful());

        // Deleting the resource with a compliant request builder doesn't work
        $delete = $query->createCommand($query::COMMAND_DELETE);
        $delete->setTerm($term);
        $query->setCommand($delete);
        $request = $compliantRequestBuilder->build($query);
        $response = self::$client->executeRequest($request);
        $result = self::$client->createResult($query, $response);
        $this->assertFalse($result->getWasSuccessful(), 'Check if SOLR-6853 is fixed.');

        // The term still exists
        $exists = $query->createCommand($query::COMMAND_EXISTS, $this->getManagedResourcesExistsCommandOptions());
        $exists->setTerm($term);
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertTrue($result->getWasSuccessful());

        // Deleting the resource with our actual request builder doesn't work
        $query->setCommand($delete);
        $request = $actualRequestBuilder->build($query);
        $response = self::$client->executeRequest($request);
        $result = self::$client->createResult($query, $response);
        $this->assertTrue($result->getWasSuccessful());

        // The term is gone
        $query->setCommand($exists);
        $result = self::$client->execute($query);
        $this->assertFalse($result->getWasSuccessful());
    }

    public function managedResourcesSolr6853Provider(): array
    {
        $term = 'test-:?#[]@% ';
        $data = [];

        $query = new StopwordsQuery();
        $add = $query->createCommand($query::COMMAND_ADD);
        $add->setStopwords([$term]);

        $data['stopwords'] = ['stopwords', $query, $add];

        $query = new SynonymsQuery();
        $add = $query->createCommand($query::COMMAND_ADD);
        $synonyms = new Synonyms();
        $synonyms->setTerm($term);
        $synonyms->setSynonyms(['foo', 'bar']);
        $add->setSynonyms($synonyms);

        $data['synonyms'] = ['synonyms', $query, $add];

        return $data;
    }

    public function testGetBodyOnHttpError()
    {
        /** @var \Solarium\Core\Query\Status4xxNoExceptionInterface $query */
        $query = self::$client->createManagedSynonyms();
        $query->setName('english');
        $query->setTerm('foo');

        $result = self::$client->execute($query);

        $this->assertFalse($result->getWasSuccessful());
        $this->assertNotSame('', $result->getResponse()->getBody());
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

class NonControlCharFilteringHelper extends Helper
{
    public function filterControlCharacters(string $data): string
    {
        return $data;
    }
}

class NonControlCharFilteringUpdateQuery extends UpdateQuery
{
    /**
     * Get a requestbuilder that doesn't filter control characters.
     *
     * @return UpdateRequestBuilder
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new NonControlCharFilteringUpdateRequestBuilder();
    }
}

class NonControlCharFilteringUpdateRequestBuilder extends UpdateRequestBuilder
{
    /**
     * Get a custom helper instance that doesn't filter control characters.
     *
     * @return Helper
     */
    public function getHelper(): Helper
    {
        if (null === $this->helper) {
            $this->helper = new NonControlCharFilteringHelper();
        }

        return $this->helper;
    }
}

/**
 * Request builder for a managed resource that percent-encodes the list/map name
 * and term once, in compliance wiht RFC 3986.
 *
 * It doesn't apply the double percent-encoding required to work around SOLR-6853
 *
 * @see https://issues.apache.org/jira/browse/SOLR-6853
 */
class CompliantManagedResourceRequestBuilder extends ResourceRequestBuilder
{
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);

        // Undo the double percent-encoding to end up with single encoding
        $handlerSegments = explode('/', $request->getHandler());
        foreach ($handlerSegments as &$segment) {
            $segment = rawurldecode($segment);
        }
        $request->setHandler(implode('/', $handlerSegments));

        return $request;
    }
}
