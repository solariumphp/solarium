<?php

namespace Solarium\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Highlighting\Highlighting;
use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTraits\GroupingTrait;
use Solarium\Component\QueryTraits\TermsTrait;
use Solarium\Component\Result\Facet\Pivot\PivotItem;
use Solarium\Component\Result\Grouping\FieldGroup;
use Solarium\Component\Result\Grouping\QueryGroup;
use Solarium\Component\Result\Grouping\Result as GroupingResult;
use Solarium\Component\Result\Grouping\ValueGroup;
use Solarium\Component\Result\Terms\Result as TermsResult;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\Events;
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
use Solarium\Plugin\Loadbalancer\Event\EndpointFailure as LoadbalancerEndpointFailureEvent;
use Solarium\Plugin\Loadbalancer\Event\Events as LoadbalancerEvents;
use Solarium\Plugin\Loadbalancer\Loadbalancer;
use Solarium\Plugin\ParallelExecution\ParallelExecution;
use Solarium\Plugin\PrefetchIterator;
use Solarium\QueryType\Luke\Query as LukeQuery;
use Solarium\QueryType\Luke\Result\Doc\DocFieldInfo as LukeDocFieldInfo;
use Solarium\QueryType\Luke\Result\Doc\DocInfo as LukeDocInfo;
use Solarium\QueryType\Luke\Result\Fields\FieldInfo as LukeFieldInfo;
use Solarium\QueryType\Luke\Result\Index\Index as LukeIndexResult;
use Solarium\QueryType\Luke\Result\Schema\Schema as LukeSchemaResult;
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
use Solarium\QueryType\Update\RequestBuilder\Xml as XmlUpdateRequestBuilder;
use Solarium\Support\Utility;
use Solarium\Tests\Integration\Plugin\EventTimer;
use Solarium\Tests\Integration\Query\CustomQueryInterfaceQuery;
use Solarium\Tests\Integration\Query\CustomSelfQuery;
use Solarium\Tests\Integration\Query\CustomStaticQuery;
use Symfony\Contracts\EventDispatcher\Event;
use TRegx\PhpUnit\DataProviders\DataProvider;

abstract class AbstractTechproductsTestCase extends TestCase
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
        self::$isSolrOnWindows = str_starts_with($systemName, 'Windows');

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
                $update->setRequestFormat(UpdateQuery::REQUEST_FORMAT_XML);

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
     * This data provider can be used to test functional equivalence in parsing results
     * from the same queries with different response writers.
     */
    public function responseWriterProvider(): array
    {
        return [
            [AbstractQuery::WT_JSON],
            [AbstractQuery::WT_PHPS],
        ];
    }

    /**
     * This data provider should be used by all UpdateQuery tests that don't test request
     * format specific Commands to ensure functional equivalence between the formats.
     */
    public function updateRequestFormatProvider(): array
    {
        return [
            [UpdateQuery::REQUEST_FORMAT_XML],
            [UpdateQuery::REQUEST_FORMAT_JSON],
        ];
    }

    /**
     * This data provider crosses {@see updateRequestFormatProvider()} with
     * {@see responseWriterProvider()}.
     */
    public function crossRequestFormatResponseWriterProvider(): DataProvider
    {
        return DataProvider::cross(
            $this->updateRequestFormatProvider(),
            $this->responseWriterProvider(),
        );
    }

    /**
     * @dataProvider responseWriterProvider
     */
    public function testPing(string $responseWriter)
    {
        $ping = self::$client->createPing();
        $ping->setResponseWriter($responseWriter);
        $result = self::$client->ping($ping);
        $this->assertSame(0, $result->getStatus());
        $this->assertSame('OK', $result->getPingStatus());

        if ($this instanceof AbstractCloudTestCase) {
            $this->assertTrue($result->getZkConnected());
        } else {
            $this->assertNull($result->getZkConnected());
        }
    }

    /**
     * @dataProvider responseWriterProvider
     */
    public function testSelect(string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
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
    }

    public function testJsonSerializeSelectResult()
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter(AbstractQuery::WT_JSON);
        $result = self::$client->select($select);

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
     *
     * @dataProvider crossRequestFormatResponseWriterProvider
     */
    public function testEscapes(string $requestFormat, string $responseWriter)
    {
        $escapeChars = [' ', '+', '-', '&&', '||', '!', '(', ')', '{', '}', '[', ']', '^', '"', '~', '*', '?', ':', '/', '\\'];
        $cat = [implode('', $escapeChars)];

        foreach ($escapeChars as $char) {
            $cat[] = 'a'.$char.'b';
        }

        $update = self::$client->createUpdate();
        $update->setRequestFormat($requestFormat);
        $update->setResponseWriter($responseWriter);
        $doc = $update->createDocument();
        $doc->setField('id', 'solarium-test-escapes');
        $doc->setField('name', 'Solarium Test Escapes');
        $doc->setField('cat', $cat);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);

        // check if stored correctly in index
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
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
     * @see https://github.com/solariumphp/solarium/issues/1104
     *
     * @dataProvider crossRequestFormatResponseWriterProvider
     */
    public function testPhraseQuery(string $requestFormat, string $responseWriter)
    {
        $phrase = "^The 17\" O'Conner && O`Series \n OR a || 1%2 1~2 1*2 \r\n book? \r \twhat \\ text: }{ )( ][ - + // \n\r ok? end$";

        $update = self::$client->createUpdate();
        $update->setResponseWriter($responseWriter);
        $update->setRequestFormat($requestFormat);
        $doc = $update->createDocument();
        $doc->setField('id', 'solarium-test-phrase');
        $doc->setField('name', 'Solarium Test Phrase Query');
        $doc->setField('cat', [$phrase]);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);

        if ($update::REQUEST_FORMAT_XML === $requestFormat) {
            /*
             * Per https://www.w3.org/TR/REC-xml/#sec-line-ends line breaks are normalized
             *
             *      [...] by translating both the two-character sequence #xD #xA and
             *      any #xD that is not followed by #xA to a single #xA character.
             */
            $phrase = str_replace(["\r\n", "\r"], ["\n", "\n"], $phrase);
        }

        // check if stored correctly in index
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
        $select->setQuery('id:solarium-test-phrase');
        $result = self::$client->select($select);
        $this->assertSame([$phrase], $result->getIterator()->current()->getFields()['cat']);

        // as term
        $select->setQuery('cat:%T1%', [$phrase]);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame('solarium-test-phrase', $result->getIterator()->current()->getFields()['id']);

        // as phrase
        $select->setQuery('cat:%P1%', [$phrase]);
        $result = self::$client->select($select);
        $this->assertCount(1, $result);
        $this->assertSame('solarium-test-phrase', $result->getIterator()->current()->getFields()['id']);

        // cleanup
        $update->addDeleteQuery('cat:%P1%', [$phrase]);
        $update->addCommit(true, true);
        self::$client->update($update);
        $select->setQuery('id:solarium-test-phrase');
        $result = self::$client->select($select);
        $this->assertCount(0, $result);
    }

    /**
     * @see https://github.com/solariumphp/solarium/issues/974
     * @see https://solr.apache.org/guide/local-parameters-in-queries.html#basic-syntax-of-local-parameters
     *
     * @dataProvider crossRequestFormatResponseWriterProvider
     */
    public function testLocalParamValueEscapes(string $requestFormat, string $responseWriter)
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
        $update->setResponseWriter($responseWriter);
        $update->setRequestFormat($requestFormat);
        $doc = $update->createDocument();
        $doc->setField('id', 'solarium-test-localparamvalue-escapes');
        $doc->setField('name', 'Solarium Test Local Param Value Escapes');
        $doc->setField('cat', $categories);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);

        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
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

    /**
     * @dataProvider responseWriterProvider
     */
    public function testRangeQueries(string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);

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
     * @see https://solr.apache.org/guide/solr/latest/query-guide/faceting.html#combining-stats-component-with-pivots
     *
     * @dataProvider responseWriterProvider
     */
    public function testFacetPivotsWithStatsComponent(string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);

        $facetSet = $select->getFacetSet();
        $facet = $facetSet->createFacetPivot('piv1');
        $facet->addFields('{!stats=piv1}cat');

        $stats = $select->getStats();
        $stats->createField('{!tag=piv1 sum=true percentiles="1,10,90,99"}price');
        $stats->createField('{!tag=piv1 min=true max=true mean=true}popularity');

        $result = self::$client->select($select);
        /** @var PivotItem $pivotItem */
        $pivotItem = $result->getFacetSet()->getFacet('piv1')->getPivot()[0];
        $pivotStats = $pivotItem->getStats();
        $this->assertCount(2, $pivotStats->getResults());

        $result1 = $pivotStats->getResult('price');
        $this->assertSame('price', $result1->getName());
        $this->assertIsFloat($result1->getSum());
        $this->assertSame(['1.0', '10.0', '90.0', '99.0'], array_keys($result1->getPercentiles()));

        $result2 = $pivotStats->getResult('popularity');
        $this->assertSame('popularity', $result2->getName());
        $this->assertSame(0.0, $result2->getMin());
        $this->assertSame(10.0, $result2->getMax());
        $this->assertSame(5.25, $result2->getMean());
    }

    /**
     * @dataProvider crossHighlightingMethodResponseWriterProvider
     */
    public function testHighlightingComponentMethods(string $method, string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
        // The self-defined "componentdemo" request handler has a highlighting component.
        $select->setHandler('componentdemo');
        $select->setQuery('id:F8V7067-APL-KIT');

        $highlighting = $select->getHighlighting();
        $highlighting->setMethod($method);
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

    public function crossHighlightingMethodResponseWriterProvider(): DataProvider
    {
        $highlightingMethods = [
            [Highlighting::METHOD_UNIFIED],
            [Highlighting::METHOD_ORIGINAL],
            [Highlighting::METHOD_FASTVECTOR],
        ];

        return DataProvider::cross(
            $highlightingMethods,
            $this->responseWriterProvider(),
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
     * @dataProvider responseWriterProvider
     *
     * @group skip_for_solr_cloud
     */
    public function testGroupingComponent(string $responseWriter)
    {
        self::$client->registerQueryType('grouping', GroupingTestQuery::class);
        /** @var GroupingTestQuery $select */
        $select = self::$client->createQuery('grouping');
        $select->setResponseWriter($responseWriter);
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
     * @see https://issues.apache.org/jira/browse/SOLR-13839
     * @see https://issues.apache.org/jira/browse/SOLR-6612
     *
     * @dataProvider responseWriterProvider
     *
     * @group skip_for_solr_cloud
     */
    public function testGroupingComponentFixForSolr13839(string $responseWriter)
    {
        self::$client->registerQueryType('grouping', GroupingTestQuery::class);
        /** @var GroupingTestQuery $select */
        $select = self::$client->createQuery('grouping');
        $select->setResponseWriter($responseWriter);
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

    /**
     * @dataProvider responseWriterProvider
     */
    public function testMoreLikeThisComponent(string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
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
        if (8 <= self::$solrVersion && $this instanceof AbstractServerTestCase) {
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
     * @dataProvider responseWriterProvider
     *
     * @group skip_for_solr_cloud
     */
    public function testMoreLikeThisQuery(string $responseWriter)
    {
        $query = self::$client->createMoreLikethis();
        $query->setResponseWriter($responseWriter);

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
     * @dataProvider responseWriterProvider
     *
     * @group skip_for_solr_cloud
     */
    public function testMoreLikeThisStream(string $responseWriter)
    {
        $query = self::$client->createMoreLikethis();
        $query->setResponseWriter($responseWriter);

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

    /**
     * @dataProvider responseWriterProvider
     */
    public function testQueryElevation(string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
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

    /**
     * @dataProvider responseWriterProvider
     */
    public function testSpatial(string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);

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

    /**
     * @dataProvider responseWriterProvider
     */
    public function testSuggester(string $responseWriter)
    {
        $suggester = self::$client->createSuggester();
        $suggester->setResponseWriter($responseWriter);
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

    /**
     * @dataProvider responseWriterProvider
     */
    public function testTerms(string $responseWriter)
    {
        $terms = self::$client->createTerms();
        $terms->setResponseWriter($responseWriter);
        $terms->setFields('name');

        // Setting distrib to true in a non cloud setup causes exceptions.
        if ($this instanceof AbstractCloudTestCase) {
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

    /**
     * @dataProvider responseWriterProvider
     */
    public function testTermsComponent(string $responseWriter)
    {
        self::$client->registerQueryType('test', TermsTestQuery::class);
        $select = self::$client->createQuery('test');
        $select->setResponseWriter($responseWriter);

        // Setting distrib to true in a non cloud setup causes exceptions.
        if ($this instanceof AbstractCloudTestCase) {
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

    /**
     * @dataProvider responseWriterProvider
     */
    public function testTermVectorComponent(string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
        $select->setHandler('tvrh');
        $select->setQuery($select->getHelper()->rangeQuery('includes', null, null));
        $select->addField('[docid]');
        // we want this to be the first document so we can easily get its [docid]
        $select->setSorts(['eq(id, "9885A004")' => $select::SORT_DESC]);

        $termVectorComponent = $select->getTermVector();
        $termVectorComponent->setFields('includes');
        $termVectorComponent->setAll(true);

        $result = self::$client->select($select);
        $termVector = $result->getTermVector();
        $warnings = $termVector->getWarnings();
        $document = $termVector->getDocument('9885A004');
        $field = $document->getField('includes');
        $term = $field->getTerm('cable');

        $this->assertCount(\count($result), $termVector);
        $this->assertSame(['includes'], $warnings->getNoPayloads());
        $this->assertNotNull($document);
        $this->assertSame('9885A004', $document->getUniqueKey());
        $this->assertNotNull($field);
        $this->assertSame('includes', $field->getName());
        $this->assertNotNull($term);
        $this->assertSame('cable', $term->getTerm());
        $this->assertSame(2, $term->getTermFrequency());
        $this->assertSame([4, 6], $term->getPositions());
        $this->assertSame([['start' => 18, 'end' => 23], ['start' => 28, 'end' => 33]], $term->getOffsets());
        $this->assertNull($term->getPayloads());

        // distributed document and term statistics can introduce inaccuracies
        if ($this instanceof AbstractServerTestCase) {
            $this->assertSame(3, $term->getDocumentFrequency());
            $this->assertSame(2 / 3, $term->getTermFreqInverseDocFreq());
        }

        // we would need to know which shard to query in SolrCloud
        if ($this instanceof AbstractServerTestCase) {
            $termVectorComponent->setDocIds([$result->getDocuments()[0]['[docid]']]);

            $result = self::$client->select($select);
            $termVector = $result->getTermVector();

            $this->assertCount(1, $termVector);
            $this->assertEquals($document, $termVector['9885A004']);
        }
    }

    /**
     * @dataProvider crossRequestFormatResponseWriterProvider
     */
    public function testUpdate(string $requestFormat, string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
        $select->setQuery('cat:solarium-test');
        $select->addSort('id', $select::SORT_ASC);
        $select->setFields('id,name,price,content');

        // add, but don't commit
        $update = self::$client->createUpdate();
        $update->setRequestFormat($requestFormat);
        $update->setResponseWriter($responseWriter);
        $doc1 = $update->createDocument();
        $doc1->setField('id', 'solarium-test-1');
        $doc1->setField('name', 'Solarium Test 1');
        $doc1->setField('cat', 'solarium-test');
        $doc1->setField('price', 3.14);
        $doc1->setField('content', ['foo', 'bar']);
        $doc2 = $update->createDocument();
        $doc2->setField('id', 'solarium-test-2');
        $doc2->setField('name', 'Solarium Test 2');
        $doc2->setField('cat', 'solarium-test');
        $doc2->setField('price', 42.0);
        $doc2->setField('content', []);
        $update->addDocuments([$doc1, $doc2]);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);

        // commit
        $update = self::$client->createUpdate();
        $update->setRequestFormat($requestFormat);
        $update->setResponseWriter($responseWriter);
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(2, $result);
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-test-1',
            'name' => 'Solarium Test 1',
            'price' => 3.14,
            'content' => [
                'foo',
                'bar',
            ],
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-test-2',
            'name' => 'Solarium Test 2',
            'price' => 42.0,
        ], $iterator->current()->getFields());

        // delete by id and commit
        $update = self::$client->createUpdate();
        $update->setRequestFormat($requestFormat);
        $update->setResponseWriter($responseWriter);
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
        $update->setRequestFormat($requestFormat);
        $update->setResponseWriter($responseWriter);
        $update->addDeleteQuery('cat:solarium-test');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);

        // optimize
        $update = self::$client->createUpdate();
        $update->setRequestFormat($requestFormat);
        $update->setResponseWriter($responseWriter);
        $update->addOptimize(true, false);
        $response = self::$client->update($update);
        $this->assertSame(0, $response->getStatus());

        // rollback is currently not supported in SolrCloud mode (SOLR-4895)
        if ($this instanceof AbstractServerTestCase) {
            // add, rollback, commit
            $update = self::$client->createUpdate();
            $update->setRequestFormat($requestFormat);
            $update->setResponseWriter($responseWriter);
            $doc1 = $update->createDocument();
            $doc1->setField('id', 'solarium-test-1');
            $doc1->setField('name', 'Solarium Test 1');
            $doc1->setField('cat', 'solarium-test');
            $doc1->setField('price', 3.14);
            $update->addDocument($doc1);
            self::$client->update($update);
            $update = self::$client->createUpdate();
            $update->setRequestFormat($requestFormat);
            $update->addRollback();
            $update->addCommit(true, true);
            self::$client->update($update);
            $result = self::$client->select($select);
            $this->assertCount(0, $result);
        }
    }

    /**
     * @dataProvider responseWriterProvider
     */
    public function testUpdateRawXml(string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
        $select->setQuery('cat:solarium-test');
        $select->addSort('id', $select::SORT_ASC);
        $select->setFields('id,name,price,content');

        // raw add and raw commit
        $update = self::$client->createUpdate();
        $update->setRequestFormat(UpdateQuery::REQUEST_FORMAT_XML);
        $update->setResponseWriter($responseWriter);
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
        $update->setRequestFormat(UpdateQuery::REQUEST_FORMAT_XML);
        $update->setResponseWriter($responseWriter);
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
        $update->setRequestFormat(UpdateQuery::REQUEST_FORMAT_XML);
        $update->setResponseWriter($responseWriter);
        $update->addRawXmlCommand('<delete><query>cat:solarium-test</query></delete>');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);

        // add from UTF-8 encoded files without and with Byte Order Mark and XML declaration
        $update = self::$client->createUpdate();
        $update->setRequestFormat(UpdateQuery::REQUEST_FORMAT_XML);
        $update->setResponseWriter($responseWriter);
        foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testxml[1234]-add*.xml') as $file) {
            $update->addRawXmlFile($file);
        }
        $update->addCommit(true, true);
        self::$client->update($update);

        // add from non-UTF-8 encoded file
        $update = self::$client->createUpdate();
        $update->setRequestFormat(UpdateQuery::REQUEST_FORMAT_XML);
        $update->setResponseWriter($responseWriter);
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
        $update->setRequestFormat(UpdateQuery::REQUEST_FORMAT_XML);
        $update->setResponseWriter($responseWriter);
        $update->addRawXmlFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testxml6-delete.xml');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);
    }

    /**
     * @dataProvider updateRequestFormatProvider
     */
    public function testModifiers(string $requestFormat)
    {
        $select = self::$client->createSelect();
        $select->setQuery('id:solarium-test');
        $select->addSort('id', $select::SORT_ASC);
        $select->setFields('id,name,cat,weight');
        $update = self::$client->createUpdate();
        $update->setRequestFormat($requestFormat);

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
        if (7 === self::$solrVersion && $this instanceof AbstractCloudTestCase) {
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
        $update->setRequestFormat($requestFormat);
        $update->addDeleteById('solarium-test');
        $update->addCommit(true, true);
        self::$client->update($update);
        $result = self::$client->select($select);
        $this->assertCount(0, $result);
    }

    /**
     * @dataProvider updateRequestFormatProvider
     */
    public function testNestedDocuments(string $requestFormat)
    {
        $data = [
            'id' => 'solarium-parent',
            'name' => 'Solarium Nested Document Parent',
            'cat' => ['solarium-nested-document', 'parent'],
            'single_child' => [
                'id' => 'solarium-single-child',
                'name' => 'Solarium Nested Document Single Child',
                'cat' => ['solarium-nested-document', 'child'],
                'weight' => 0.0,
            ],
            'children' => [
                [
                    'id' => 'solarium-child-1',
                    'name' => 'Solarium Nested Document Child 1',
                    'cat' => ['solarium-nested-document', 'child'],
                    'weight' => 1.0,
                    'grandchildren' => [
                        [
                            'id' => 'solarium-grandchild-1-1',
                            'name' => 'Solarium Nested Document Grandchild 1.1',
                            'cat' => ['solarium-nested-document', 'grandchild'],
                            'weight' => 1.1,
                        ],
                    ],
                ],
                [
                    'id' => 'solarium-child-2',
                    'name' => 'Solarium Nested Document Child 2',
                    'cat' => ['solarium-nested-document', 'child'],
                    'weight' => 2.0,
                    'grandchildren' => [
                        [
                            'id' => 'solarium-grandchild-2-1',
                            'name' => 'Solarium Nested Document Grandchild 2.1',
                            'cat' => ['solarium-nested-document', 'grandchild'],
                            'weight' => 2.1,
                        ],
                    ],
                ],
            ],
        ];

        $update = self::$client->createUpdate();
        $update->setRequestFormat($requestFormat);
        $doc = $update->createDocument($data);
        $update->addDocument($doc);
        $update->addCommit(true, true);
        self::$client->update($update);

        // get all documents (parents and descendants) as a flat list
        $select = self::$client->createSelect();
        $select->setQuery('cat:solarium-nested-document');
        $select->setFields('id,name,weight');
        $result = self::$client->select($select);
        $this->assertCount(6, $result);

        // without a sort, children are returned before their parents because they're added in that order to the underlying Lucene index
        $iterator = $result->getIterator();
        $this->assertSame([
            'id' => 'solarium-single-child',
            'name' => 'Solarium Nested Document Single Child',
            'weight' => 0.0,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-grandchild-1-1',
            'name' => 'Solarium Nested Document Grandchild 1.1',
            'weight' => 1.1,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-child-1',
            'name' => 'Solarium Nested Document Child 1',
            'weight' => 1.0,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-grandchild-2-1',
            'name' => 'Solarium Nested Document Grandchild 2.1',
            'weight' => 2.1,
        ], $iterator->current()->getFields());
        $iterator->next();
        $this->assertSame([
            'id' => 'solarium-child-2',
            'name' => 'Solarium Nested Document Child 2',
            'weight' => 2.0,
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

            $expected = [
                'id' => 'solarium-parent',
                'single_child' => [
                    'id' => 'solarium-single-child',
                ],
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
            ];

            if (UpdateQuery::REQUEST_FORMAT_XML === $requestFormat && 9 > self::$solrVersion) {
                // labelled single nested child documents can't be indexed in XML before Solr 9.3 (SOLR-16183)
                unset($expected['single_child']);
            }

            $this->assertSame($expected, $iterator->current()->getFields());

            // only get descendant documents that match a filter
            $select->setFields('id,single_child,weight,children,grandchildren,[child childFilter=weight:2.1]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();
            $this->assertSame([
                'id' => 'solarium-parent',
                'children' => [
                    [
                        'id' => 'solarium-child-2',
                        'weight' => 2.0,
                        'grandchildren' => [
                            [
                                'id' => 'solarium-grandchild-2-1',
                                'weight' => 2.1,
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

            $expected = [
                'id' => 'solarium-parent',
                'single_child' => [
                    'id' => 'solarium-single-child',
                ],
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
            ];

            if (UpdateQuery::REQUEST_FORMAT_XML === $requestFormat && 9 > self::$solrVersion) {
                // labelled single nested child documents can't be indexed in XML before Solr 9.3 (SOLR-16183)
                unset($expected['single_child']);
            }

            $this->assertSame($expected, $iterator->current()->getFields());

            // only return a subset of the top level fl parameter for the child documents
            $select->setFields('id,name,weight,single_child,children,grandchildren,[child fl=id,weight]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();

            $expected = [
                'id' => 'solarium-parent',
                'name' => 'Solarium Nested Document Parent',
                'single_child' => [
                    'id' => 'solarium-single-child',
                    'weight' => 0.0,
                ],
                'children' => [
                    [
                        'id' => 'solarium-child-1',
                        'weight' => 1.0,
                        'grandchildren' => [
                            [
                                'id' => 'solarium-grandchild-1-1',
                                'weight' => 1.1,
                            ],
                        ],
                    ],
                    [
                        'id' => 'solarium-child-2',
                        'weight' => 2.0,
                        'grandchildren' => [
                            [
                                'id' => 'solarium-grandchild-2-1',
                                'weight' => 2.1,
                            ],
                        ],
                    ],
                ],
            ];

            if (UpdateQuery::REQUEST_FORMAT_XML === $requestFormat && 9 > self::$solrVersion) {
                // labelled single nested child documents can't be indexed in XML before Solr 9.3 (SOLR-16183)
                unset($expected['single_child']);
            }

            $this->assertSame($expected, $iterator->current()->getFields());
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
                    'weight' => 3.0,
                ],
                [
                    'id' => 'solarium-child-4',
                    'name' => 'Solarium Nested Document Child 4',
                    'cat' => ['solarium-nested-document', 'child'],
                    'weight' => 4.0,
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
            $select->setFields('id,name,cat,weight,children,grandchildren,[child]');
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
                        'weight' => 3.0,
                    ],
                    [
                        'id' => 'solarium-child-4',
                        'name' => 'Solarium Nested Document Child 4',
                        'cat' => ['solarium-nested-document', 'child'],
                        'weight' => 4.0,
                    ],
                ],
            ], $iterator->current()->getFields());

            // non-monolithic atomic updates (replacing, adding, removing individual child documents) can't be executed through XML (SOLR-12677)
            if (UpdateQuery::REQUEST_FORMAT_JSON === $requestFormat) {
                // atomic update: adding a child document to a pseudo-field
                $newChild = [
                    'id' => 'solarium-child-5',
                    'name' => 'Solarium Nested Document Added Child 5',
                    'cat' => ['solarium-nested-document', 'child', 'added'],
                    'weight' => 5.0,
                ];
                $doc = $update->createDocument();
                $doc->setKey('id', 'solarium-parent');
                $doc->setField('cat', 'updated-2');
                $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
                $doc->setField('children', $newChild);
                $doc->setFieldModifier('children', $doc::MODIFIER_ADD);
                $update->addDocument($doc);
                $update->addCommit(true, true);
                self::$client->update($update);
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
                    'children' => [
                        [
                            'id' => 'solarium-child-3',
                            'name' => 'Solarium Nested Document Child 3',
                            'cat' => ['solarium-nested-document', 'child'],
                            'weight' => 3.0,
                        ],
                        [
                            'id' => 'solarium-child-4',
                            'name' => 'Solarium Nested Document Child 4',
                            'cat' => ['solarium-nested-document', 'child'],
                            'weight' => 4.0,
                        ],
                        [
                            'id' => 'solarium-child-5',
                            'name' => 'Solarium Nested Document Added Child 5',
                            'cat' => ['solarium-nested-document', 'child', 'added'],
                            'weight' => 5.0,
                        ],
                    ],
                ], $iterator->current()->getFields());

                // atomic update: adding a list of child documents to a pseudo-field
                $newChildren = [
                    [
                        'id' => 'solarium-child-6',
                        'name' => 'Solarium Nested Document Added Child 6',
                        'cat' => ['solarium-nested-document', 'child', 'added'],
                        'weight' => 6.0,
                    ],
                    [
                        'id' => 'solarium-child-7',
                        'name' => 'Solarium Nested Document Added Child 7',
                        'cat' => ['solarium-nested-document', 'child', 'added'],
                        'weight' => 7.0,
                    ],
                ];
                $doc = $update->createDocument();
                $doc->setKey('id', 'solarium-parent');
                $doc->setField('cat', 'updated-3');
                $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
                $doc->setField('children', $newChildren);
                $doc->setFieldModifier('children', $doc::MODIFIER_ADD);
                $update->addDocument($doc);
                $update->addCommit(true, true);
                self::$client->update($update);
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
                        'updated-3',
                    ],
                    'children' => [
                        [
                            'id' => 'solarium-child-3',
                            'name' => 'Solarium Nested Document Child 3',
                            'cat' => ['solarium-nested-document', 'child'],
                            'weight' => 3.0,
                        ],
                        [
                            'id' => 'solarium-child-4',
                            'name' => 'Solarium Nested Document Child 4',
                            'cat' => ['solarium-nested-document', 'child'],
                            'weight' => 4.0,
                        ],
                        [
                            'id' => 'solarium-child-5',
                            'name' => 'Solarium Nested Document Added Child 5',
                            'cat' => ['solarium-nested-document', 'child', 'added'],
                            'weight' => 5.0,
                        ],
                        [
                            'id' => 'solarium-child-6',
                            'name' => 'Solarium Nested Document Added Child 6',
                            'cat' => ['solarium-nested-document', 'child', 'added'],
                            'weight' => 6.0,
                        ],
                        [
                            'id' => 'solarium-child-7',
                            'name' => 'Solarium Nested Document Added Child 7',
                            'cat' => ['solarium-nested-document', 'child', 'added'],
                            'weight' => 7.0,
                        ],
                    ],
                ], $iterator->current()->getFields());

                // add-or-replace logic for child documents is available since Solr 9.0.0 (SOLR-15213)
                if (9 <= self::$solrVersion) {
                    // atomic update: replacing a list of child documents in a pseudo-field
                    $newChildren = [
                        [
                            'id' => 'solarium-child-3',
                            'name' => 'Solarium Nested Document Updated Child 3',
                            'cat' => ['solarium-nested-document', 'child', 'updated-4'],
                            'weight' => 3.4,
                        ],
                        [
                            'id' => 'solarium-child-5',
                            'name' => 'Solarium Nested Document Updated Child 5',
                            'cat' => ['solarium-nested-document', 'child', 'updated-4'],
                            'weight' => 5.4,
                        ],
                    ];
                    $doc = $update->createDocument();
                    $doc->setKey('id', 'solarium-parent');
                    $doc->setField('cat', 'updated-4');
                    $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
                    $doc->setField('children', $newChildren);
                    $doc->setFieldModifier('children', $doc::MODIFIER_ADD);
                    $update->addDocument($doc);
                    $update->addCommit(true, true);
                    self::$client->update($update);
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
                            'updated-3',
                            'updated-4',
                        ],
                        'children' => [
                            [
                                'id' => 'solarium-child-3',
                                'name' => 'Solarium Nested Document Updated Child 3',
                                'cat' => ['solarium-nested-document', 'child', 'updated-4'],
                                'weight' => 3.4,
                            ],
                            [
                                'id' => 'solarium-child-4',
                                'name' => 'Solarium Nested Document Child 4',
                                'cat' => ['solarium-nested-document', 'child'],
                                'weight' => 4.0,
                            ],
                            [
                                'id' => 'solarium-child-5',
                                'name' => 'Solarium Nested Document Updated Child 5',
                                'cat' => ['solarium-nested-document', 'child', 'updated-4'],
                                'weight' => 5.4,
                            ],
                            [
                                'id' => 'solarium-child-6',
                                'name' => 'Solarium Nested Document Added Child 6',
                                'cat' => ['solarium-nested-document', 'child', 'added'],
                                'weight' => 6.0,
                            ],
                            [
                                'id' => 'solarium-child-7',
                                'name' => 'Solarium Nested Document Added Child 7',
                                'cat' => ['solarium-nested-document', 'child', 'added'],
                                'weight' => 7.0,
                            ],
                        ],
                    ], $iterator->current()->getFields());

                    // atomic update: replacing a child document in a pseudo-field
                    // (revert previous update to solarium-child-5 to keep tests consistent across Solr versions)
                    $newChild = [
                        'id' => 'solarium-child-5',
                        'name' => 'Solarium Nested Document Added Child 5',
                        'cat' => ['solarium-nested-document', 'child', 'added'],
                        'weight' => 5.0,
                    ];
                    $doc = $update->createDocument();
                    $doc->setKey('id', 'solarium-parent');
                    $doc->setField('cat', 'updated-5');
                    $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
                    $doc->setField('children', $newChild);
                    $doc->setFieldModifier('children', $doc::MODIFIER_ADD);
                    $update->addDocument($doc);
                    $update->addCommit(true, true);
                    self::$client->update($update);
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
                            'updated-3',
                            'updated-4',
                            'updated-5',
                        ],
                        'children' => [
                            [
                                'id' => 'solarium-child-3',
                                'name' => 'Solarium Nested Document Updated Child 3',
                                'cat' => ['solarium-nested-document', 'child', 'updated-4'],
                                'weight' => 3.4,
                            ],
                            [
                                'id' => 'solarium-child-4',
                                'name' => 'Solarium Nested Document Child 4',
                                'cat' => ['solarium-nested-document', 'child'],
                                'weight' => 4.0,
                            ],
                            [
                                'id' => 'solarium-child-5',
                                'name' => 'Solarium Nested Document Added Child 5',
                                'cat' => ['solarium-nested-document', 'child', 'added'],
                                'weight' => 5.0,
                            ],
                            [
                                'id' => 'solarium-child-6',
                                'name' => 'Solarium Nested Document Added Child 6',
                                'cat' => ['solarium-nested-document', 'child', 'added'],
                                'weight' => 6.0,
                            ],
                            [
                                'id' => 'solarium-child-7',
                                'name' => 'Solarium Nested Document Added Child 7',
                                'cat' => ['solarium-nested-document', 'child', 'added'],
                                'weight' => 7.0,
                            ],
                        ],
                    ], $iterator->current()->getFields());
                } else {
                    // atomic update tests are designed to cancel each other out for any Solr version
                    // but the remainder of the test assumes 'cat' has been updated every time
                    $doc = $update->createDocument();
                    $doc->setKey('id', 'solarium-parent');
                    $doc->setField('cat', ['updated-4', 'updated-5']);
                    $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
                    $update->addDocument($doc);
                    $update->addCommit(true, true);
                }

                // atomic update: remove a child document from a pseudo-field
                $removeChild = [
                    'id' => 'solarium-child-3',
                ];
                $doc = $update->createDocument();
                $doc->setKey('id', 'solarium-parent');
                $doc->setField('cat', 'updated-6');
                $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
                $doc->setField('children', $removeChild);
                $doc->setFieldModifier('children', $doc::MODIFIER_REMOVE);
                $update->addDocument($doc);
                $update->addCommit(true, true);
                self::$client->update($update);
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
                        'updated-3',
                        'updated-4',
                        'updated-5',
                        'updated-6',
                    ],
                    'children' => [
                        [
                            'id' => 'solarium-child-4',
                            'name' => 'Solarium Nested Document Child 4',
                            'cat' => ['solarium-nested-document', 'child'],
                            'weight' => 4.0,
                        ],
                        [
                            'id' => 'solarium-child-5',
                            'name' => 'Solarium Nested Document Added Child 5',
                            'cat' => ['solarium-nested-document', 'child', 'added'],
                            'weight' => 5.0,
                        ],
                        [
                            'id' => 'solarium-child-6',
                            'name' => 'Solarium Nested Document Added Child 6',
                            'cat' => ['solarium-nested-document', 'child', 'added'],
                            'weight' => 6.0,
                        ],
                        [
                            'id' => 'solarium-child-7',
                            'name' => 'Solarium Nested Document Added Child 7',
                            'cat' => ['solarium-nested-document', 'child', 'added'],
                            'weight' => 7.0,
                        ],
                    ],
                ], $iterator->current()->getFields());

                // atomic update: remove a list of child documents from a pseudo-field
                $removeChildren = [
                    [
                        'id' => 'solarium-child-6',
                    ],
                    [
                        'id' => 'solarium-child-7',
                    ],
                ];
                $doc = $update->createDocument();
                $doc->setKey('id', 'solarium-parent');
                $doc->setField('cat', 'updated-7');
                $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
                $doc->setField('children', $removeChildren);
                $doc->setFieldModifier('children', $doc::MODIFIER_REMOVE);
                $update->addDocument($doc);
                $update->addCommit(true, true);
                self::$client->update($update);
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
                        'updated-3',
                        'updated-4',
                        'updated-5',
                        'updated-6',
                        'updated-7',
                    ],
                    'children' => [
                        [
                            'id' => 'solarium-child-4',
                            'name' => 'Solarium Nested Document Child 4',
                            'cat' => ['solarium-nested-document', 'child'],
                            'weight' => 4.0,
                        ],
                        [
                            'id' => 'solarium-child-5',
                            'name' => 'Solarium Nested Document Added Child 5',
                            'cat' => ['solarium-nested-document', 'child', 'added'],
                            'weight' => 5.0,
                        ],
                    ],
                ], $iterator->current()->getFields());

                // atomic update: set a single child document in a pseudo-field
                $newChild = [
                    'id' => 'solarium-new-single-child',
                    'name' => 'Solarium Nested Document New Single Child',
                    'cat' => ['solarium-nested-document', 'child', 'updated-8'],
                    'weight' => 0.8,
                ];
                $doc = $update->createDocument();
                $doc->setKey('id', 'solarium-parent');
                $doc->setField('cat', 'updated-8');
                $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
                $doc->setField('single_child', $newChild);
                $doc->setFieldModifier('single_child', $doc::MODIFIER_SET);
                $update->addDocument($doc);
                $update->addCommit(true, true);
                self::$client->update($update);
                $select->setQuery('id:solarium-parent');
                $select->setFields('id,name,cat,weight,single_child,[child]');
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
                        'updated-3',
                        'updated-4',
                        'updated-5',
                        'updated-6',
                        'updated-7',
                        'updated-8',
                    ],
                    'single_child' => [
                        'id' => 'solarium-new-single-child',
                        'name' => 'Solarium Nested Document New Single Child',
                        'cat' => ['solarium-nested-document', 'child', 'updated-8'],
                        'weight' => 0.8,
                    ],
                ], $iterator->current()->getFields());

                // atomic update: remove a single child document from a pseudo-field
                $doc = $update->createDocument();
                $doc->setKey('id', 'solarium-parent');
                $doc->setField('cat', 'updated-9');
                $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
                // to remove atomically, modifier must be supplied to setField()
                $doc->setField('single_child', null, null, $doc::MODIFIER_SET);
                $update->addDocument($doc);
                $update->addCommit(true, true);
                self::$client->update($update);
                $select->setQuery('id:solarium-parent');
                $select->setFields('id,name,cat,weight,single_child,[child]');
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
                        'updated-3',
                        'updated-4',
                        'updated-5',
                        'updated-6',
                        'updated-7',
                        'updated-8',
                        'updated-9',
                    ],
                ], $iterator->current()->getFields());
            }

            // atomic update: removing all child documents from a pseudo-field
            $doc = $update->createDocument();
            $doc->setKey('id', 'solarium-parent');
            $doc->setField('cat', 'updated-10');
            $doc->setFieldModifier('cat', $doc::MODIFIER_ADD);
            $doc->setField('children', []);
            $doc->setFieldModifier('children', $doc::MODIFIER_SET);
            $update->addDocument($doc);
            $update->addCommit(true, true);
            self::$client->update($update);
            $select->setQuery('id:solarium-parent');
            $select->setFields('id,name,cat,weight,children,grandchildren,[child]');
            $result = self::$client->select($select);
            $this->assertCount(1, $result);
            $iterator = $result->getIterator();

            $expected = [
                'id' => 'solarium-parent',
                'name' => 'Solarium Nested Document Parent',
                'cat' => [
                    'solarium-nested-document',
                    'parent',
                    'updated-1',
                    'updated-10',
                ],
            ];

            if (UpdateQuery::REQUEST_FORMAT_JSON === $requestFormat) {
                $expected['cat'] = [
                    'solarium-nested-document',
                    'parent',
                    'updated-1',
                    'updated-2',
                    'updated-3',
                    'updated-4',
                    'updated-5',
                    'updated-6',
                    'updated-7',
                    'updated-8',
                    'updated-9',
                    'updated-10',
                ];
            }

            $this->assertSame($expected, $iterator->current()->getFields());
        }

        // cleanup
        $update = self::$client->createUpdate();
        $update->setRequestFormat($requestFormat);
        // in Solr 7, the whole block of parent-children documents must be deleted together
        if (7 === self::$solrVersion) {
            $update->addDeleteQuery('cat:solarium-nested-document');
        }
        // from Solr 8, you can simply delete-by-ID using the id of the root document
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

    /**
     * @dataProvider updateRequestFormatProvider
     */
    public function testAnonymouslyNestedDocuments(string $requestFormat)
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
        $update->setRequestFormat($requestFormat);
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

        // cleanup
        $update = self::$client->createUpdate();
        $update->setRequestFormat($requestFormat);
        // in Solr 7, the whole block of parent-children documents must be deleted together
        if (7 === self::$solrVersion) {
            $update->addDeleteQuery('cat:solarium-nested-document');
        }
        // from Solr 8, you can simply delete-by-ID using the id of the root document
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

    /**
     * @dataProvider responseWriterProvider
     */
    public function testReRankQuery(string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
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

    /**
     * Only tested with default request format because this test deliberately
     * alters the index state for {@see testBufferedDelete()}.
     *
     * Dependencies and data providers don't mix in the way that we need them to
     * to repeat these tests for multiple request formats.
     */
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
        self::$client->removePlugin('bufferedadd');
    }

    /**
     * Only tested with default request format because this test undoes the
     * changes to the index state by {@see testBufferedAdd()}.
     *
     * Dependencies and data providers don't mix in the way that we need them to
     * to repeat these tests for multiple request formats.
     *
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
        self::$client->removePlugin('buffereddelete');
        $result = self::$client->select($select);
        $this->assertSame(0, $result->getNumFound());
    }

    /**
     * @dataProvider updateRequestFormatProvider
     */
    public function testBufferedAddAndDelete(string $requestFormat)
    {
        $bufferSize = 10;

        $addBuffer = self::$client->getPlugin('bufferedadd');
        $addBuffer->setRequestFormat($requestFormat);
        $addBuffer->setBufferSize($bufferSize);

        $delBuffer = self::$client->getPlugin('buffereddelete');
        $delBuffer->setRequestFormat($requestFormat);

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
        self::$client->removePlugin('bufferedadd');
        self::$client->removePlugin('buffereddelete');
        $result = self::$client->select($select);
        $this->assertSame(0, $result->getNumFound());
    }

    /**
     * Only tested with default request format because this test deliberately
     * alters the index state for {@see testBufferedDeleteLite()}.
     *
     * Dependencies and data providers don't mix in the way that we need them to
     * to repeat these tests for multiple request formats.
     *
     * @return int Total number of added docs
     */
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
        self::$client->removePlugin('bufferedaddlite');

        return $totalDocs;
    }

    /**
     * Only tested with default request format because this test undoes the
     * changes to the index state by {@see testBufferedAddLite()}.
     *
     * Dependencies and data providers don't mix in the way that we need them to
     * to repeat these tests for multiple request formats.
     *
     * @depends testBufferedAddLite
     *
     * @param int $totalDocs Total number of docs added by {@see testBufferedAddLite()}
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
        self::$client->removePlugin('buffereddeletelite');
    }

    /**
     * @dataProvider updateRequestFormatProvider
     */
    public function testBufferedAddAndDeleteLite(string $requestFormat)
    {
        $bufferSize = 10;

        $addBuffer = self::$client->getPlugin('bufferedaddlite');
        $addBuffer->setRequestFormat($requestFormat);
        $addBuffer->setBufferSize($bufferSize);

        $delBuffer = self::$client->getPlugin('buffereddeletelite');
        $delBuffer->setRequestFormat($requestFormat);

        $weight = 0;

        for ($i = 1; $i <= 15; ++$i) {
            $data = [
                'id' => 'solarium-bufferedaddlite-'.$i,
                'cat' => 'solarium-bufferedaddlite',
                'weight' => ++$weight,
            ];
            $addBuffer->createDocument($data);
        }

        $addBuffer->flush();

        $delBuffer->addDeleteById('solarium-bufferedaddlite-8');
        $delBuffer->addDeleteById('solarium-bufferedaddlite-4');
        $delBuffer->flush();

        foreach (range('a', 'c') as $i) {
            $data = [
                'id' => 'solarium-bufferedaddlite-'.$i,
                'cat' => 'solarium-bufferedaddlite',
                'weight' => ++$weight,
            ];
            $addBuffer->createDocument($data);
        }

        $addBuffer->flush();

        $delBuffer->addDeleteQuery('cat:solarium-bufferedaddlite AND weight:[* TO 5]');
        $delBuffer->addDeleteById('solarium-bufferedaddlite-b');
        $delBuffer->flush();

        foreach (range('d', 'e') as $i) {
            $data = [
                'id' => 'solarium-bufferedaddlite-'.$i,
                'cat' => 'solarium-bufferedaddlite',
                'weight' => ++$weight,
            ];
            $addBuffer->createDocument($data);
        }

        $addBuffer->flush();

        $delBuffer->addDeleteById('solarium-bufferedaddlite-d');
        $delBuffer->addDeleteById('solarium-bufferedaddlite-13');
        $delBuffer->flush();

        // either buffer can be committed as long as the other one has been flushed
        $addBuffer->commit(null, true, true);

        $select = self::$client->createSelect();
        $select->setQuery('cat:solarium-bufferedaddlite');
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
            'solarium-bufferedaddlite-6',
            'solarium-bufferedaddlite-7',
            'solarium-bufferedaddlite-9',
            'solarium-bufferedaddlite-10',
            'solarium-bufferedaddlite-11',
            'solarium-bufferedaddlite-12',
            'solarium-bufferedaddlite-14',
            'solarium-bufferedaddlite-15',
            'solarium-bufferedaddlite-a',
            'solarium-bufferedaddlite-c',
            'solarium-bufferedaddlite-e',
            ], $ids);

        // cleanup
        $delBuffer->addDeleteQuery('cat:solarium-bufferedaddlite');
        $delBuffer->commit(null, true, true);
        self::$client->removePlugin('bufferedaddlite');
        self::$client->removePlugin('buffereddeletelite');
        $result = self::$client->select($select);
        $this->assertSame(0, $result->getNumFound());
    }

    public function testLoadbalancerFailover()
    {
        $invalidEndpointConfig = self::$config['endpoint']['localhost'];
        $invalidEndpointConfig['host'] = 'server.invalid';
        $invalidEndpointConfig['key'] = 'invalid';
        $invalidEndpoint = self::$client->createEndpoint($invalidEndpointConfig);

        /** @var Loadbalancer $loadbalancer */
        $loadbalancer = self::$client->getPlugin('loadbalancer');
        $loadbalancer->setFailoverEnabled(true);
        $loadbalancer->addEndpoint('invalid');
        $loadbalancer->addEndpoint('localhost');
        $loadbalancer->setForcedEndpointForNextQuery('invalid');

        $invalidEndpointListenerCalled = 0;
        self::$client->getEventDispatcher()->addListener(
            LoadbalancerEvents::ENDPOINT_FAILURE,
            $invalidEndpointListener = function (LoadbalancerEndpointFailureEvent $event) use (&$invalidEndpoint, &$invalidEndpointListenerCalled) {
                ++$invalidEndpointListenerCalled;
                $this->assertSame($invalidEndpoint, $event->getEndpoint());
            }
        );

        $query = self::$client->createPing();
        $result = self::$client->ping($query);

        $this->assertSame(0, $result->getStatus());
        $this->assertSame(1, $invalidEndpointListenerCalled);
        $this->assertSame('localhost', $loadbalancer->getLastEndpoint());

        // cleanup
        self::$client->getEventDispatcher()->removeListener(LoadbalancerEvents::ENDPOINT_FAILURE, $invalidEndpointListener);
        self::$client->removePlugin('loadbalancer');
        self::$client->removeEndpoint('invalid');
    }

    /**
     * @dataProvider responseWriterProvider
     *
     * @group skip_for_solr_cloud
     */
    public function testMinimumScoreFilterWithGrouping(string $responseWriter)
    {
        $filter = self::$client->getPlugin('minimumscorefilter');
        $query = self::$client->createQuery($filter::QUERY_TYPE);
        $query->setResponseWriter($responseWriter);
        $query->setQuery('*:*');

        $groupComponent = $query->getGrouping();
        $groupComponent->addField('inStock');
        $groupComponent->setNumberOfGroups(true);

        $resultset = self::$client->select($query);
        $groups = $resultset->getGrouping();

        $this->assertCount(1, $groups);

        $fieldGroup = $groups->getGroup('inStock');

        $this->assertSame(3, $fieldGroup->getNumberOfGroups());

        $values = [];
        foreach ($fieldGroup as $valueGroup) {
            $values[] = $valueGroup->getValue();
        }

        $expected = [
            (string) true,
            (string) false,
            null,
        ];
        $this->assertSame($expected, $values);

        // cleanup
        self::$client->removePlugin('minimumscorefilter');
    }

    public function testParallelExecution()
    {
        // ParallelExecution only works with Curl, pass tacitly for other adapters
        if (!(self::$client->getAdapter() instanceof Curl)) {
            $this->expectNotToPerformAssertions();

            return;
        }

        // ParallelExecution should play nice with other plugins
        self::$client->getPlugin('postbigrequest');

        $queryInStock = self::$client->createSelect()->setQuery('inStock:true')->setOmitHeader(true);
        $queryLowPrice = self::$client->createSelect()->setQuery('price:[1 TO 300]')->setOmitHeader(true);
        $queryBigRequest = self::$client->createSelect()->setQuery('price:0 OR cat:'.str_repeat(implode('', range('a', 'z')), 1000))->setOmitHeader(true);
        $queryOverrideResult = self::$client->createSelect()->setQuery('id:parallel-1')->setOmitHeader(true);
        $queryOverrideResponse = self::$client->createSelect()->setQuery('id:parallel-2')->setOmitHeader(true);
        $queryError = self::$client->createSelect()->setQuery('cat:electronics OR ')->setOmitHeader(true);

        // all query types are supported and can be mixed in a single ParallelExecution::execute() call
        $serverQuery = self::$client->createApi([
            'version' => Request::API_V1,
            'handler' => 'admin/info/properties',
        ])->setOmitHeader(true);

        $dataOverrideResult = [
            'response' => [
                'docs' => [
                    ['id' => 'parallel-1', 'name' => 'Test override result'],
                ],
                'numFound' => 1,
                'maxScore' => 1.00,
            ],
        ];
        $responseOverrideResult = new Response(json_encode($dataOverrideResult), ['HTTP 1.0 200 OK']);

        $dataOverrideResponse = [
            'response' => [
                'docs' => [
                    ['id' => 'parallel-2', 'name' => 'Test override response'],
                ],
                'numFound' => 1,
                'maxScore' => 1.00,
            ],
        ];
        $responseOverrideResponse = new Response(json_encode($dataOverrideResponse), ['HTTP 1.0 200 OK']);

        $resultInStock = self::$client->select($queryInStock);
        $resultLowPrice = self::$client->select($queryLowPrice);
        $resultBigRequest = self::$client->select($queryBigRequest);
        $resultOverrideResult = new SelectResult($queryOverrideResult, $responseOverrideResult);
        $resultOverrideResponse = new SelectResult($queryOverrideResponse, $responseOverrideResponse);
        $serverResult = self::$client->execute($serverQuery);

        // events should be dispatched as usual
        self::$client->getEventDispatcher()->addListener(
            Events::PRE_EXECUTE,
            $overrideResult = function (Event $event) use ($resultOverrideResult) {
                $query = $event->getQuery();
                // if this test fails, the listener will remain active and would cause errors for other QueryTypes
                if ($query instanceof SelectQuery && 'id:parallel-1' === $query->getQuery()) {
                    $event->setResult($resultOverrideResult);
                }
            }
        );
        self::$client->getEventDispatcher()->addListener(
            Events::PRE_EXECUTE_REQUEST,
            $overrideResponse = function (Event $event) use ($responseOverrideResponse) {
                if ('id:parallel-2' === $event->getRequest()->getParam('q')) {
                    $event->setResponse($responseOverrideResponse);
                }
            }
        );

        $invalidEndpointConfig = self::$config['endpoint']['localhost'];
        $invalidEndpointConfig['host'] = 'server.invalid';
        $invalidEndpointConfig['key'] = 'invalid';
        $invalidEndpoint = self::$client->createEndpoint($invalidEndpointConfig);

        /** @var ParallelExecution $parallel */
        $parallel = self::$client->getPlugin('parallelexecution');
        $parallel->addQuery('instock', $queryInStock);
        $parallel->addQuery('lowprice', $queryLowPrice);
        $parallel->addQuery('bigrequest', $queryBigRequest);
        $parallel->addQuery('overrideresult', $queryOverrideResult);
        $parallel->addQuery('overrideresponse', $queryOverrideResponse);
        $parallel->addQuery('error', $queryError);
        $parallel->addQuery('endpointfailure', $queryInStock, $invalidEndpoint);
        $parallel->addQuery('server', $serverQuery);
        $results = $parallel->execute();

        // ensure that result keys maintain the order that the queries were added in
        $expectedKeys = [
            'instock',
            'lowprice',
            'bigrequest',
            'overrideresult',
            'overrideresponse',
            'error',
            'endpointfailure',
            'server',
        ];
        $this->assertSame($expectedKeys, array_keys($results));

        $this->assertEquals($resultInStock, $results['instock']);
        $this->assertEquals($resultLowPrice, $results['lowprice']);
        $this->assertEquals($resultBigRequest, $results['bigrequest']);
        $this->assertEquals($resultOverrideResult, $results['overrideresult']);
        $this->assertEquals($resultOverrideResponse, $results['overrideresponse']);
        $this->assertInstanceOf(HttpException::class, $results['error']);
        $this->assertInstanceOf(HttpException::class, $results['endpointfailure']);
        $this->assertStringContainsString('HTTP request failed, ', $results['endpointfailure']->getMessage());
        $this->assertEquals($serverResult, $results['server']);

        // cleanup
        self::$client->getEventDispatcher()->removeListener(Events::PRE_EXECUTE, $overrideResult);
        self::$client->getEventDispatcher()->removeListener(Events::PRE_EXECUTE_REQUEST, $overrideResponse);
        self::$client->removePlugin('parallelexecution');
        self::$client->removePlugin('postbigrequest');
        self::$client->removeEndpoint('invalid');
    }

    /**
     * @dataProvider responseWriterProvider
     */
    public function testPrefetchIterator(string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
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

        self::$client->removePlugin('prefetchiterator');
    }

    /**
     * @dataProvider responseWriterProvider
     */
    public function testPrefetchIteratorWithCursorMark(string $responseWriter)
    {
        $select = self::$client->createSelect();
        $select->setResponseWriter($responseWriter);
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

        self::$client->removePlugin('prefetchiterator');
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

        self::$client->removePlugin('prefetchiterator');
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

        self::$client->removePlugin('prefetchiterator');
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
        $doc->foo_1 = 'bar 1';
        $extract->setDocument($doc);
        self::$client->extract($extract);

        // add HTML document
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testhtml.html');
        $doc = $extract->createDocument();
        $doc->id = 'extract-test-2-html';
        $doc->cat = ['extract-test'];
        $doc->foo_2 = 'bar 2';
        $extract->setDocument($doc);
        self::$client->extract($extract);

        // add stream
        $contents = <<<'EOF'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8">
                    <title>HTML Stream Title</title>
                </head>
                <body>
                    <p>HTML Stream Body</p>
                </body>
            </html>
            EOF;
        $file = fopen('php://memory', 'w+');
        fwrite($file, $contents);
        $extract->setFile($file);
        $doc = $extract->createDocument();
        $doc->id = 'extract-test-3-stream';
        $doc->cat = ['extract-test'];
        $doc->foo_3 = 'bar 3';
        $extract->setDocument($doc);
        self::$client->extract($extract);
        fclose($file);

        // now get the documents and check the contents
        $select = self::$client->createSelect();
        $select->setQuery('cat:extract-test');
        $select->addSort('id', $select::SORT_ASC);
        $selectResult = self::$client->select($select);
        $this->assertCount(3, $selectResult);
        $iterator = $selectResult->getIterator();

        /** @var Document $document */
        $document = $iterator->current();
        $this->assertSame('application/pdf', $document['content_type'][0], 'Written document does not contain extracted content type');
        $this->assertSame('PDF Test', trim($document['content'][0]), 'Written document does not contain extracted result');
        $this->assertSame(['bar 1'], $document['attr_foo_1']);
        $iterator->next();
        $document = $iterator->current();
        $this->assertSame('text/html; charset=UTF-8', $document['content_type'][0], 'Written document does not contain extracted content type');
        $this->assertSame('HTML Test Title', $document['title'][0], 'Written document does not contain extracted title');
        $this->assertMatchesRegularExpression('/^HTML Test Title\s+HTML Test Body$/', trim($document['content'][0]), 'Written document does not contain extracted result');
        $this->assertSame(['bar 2'], $document['attr_foo_2']);
        $iterator->next();
        $document = $iterator->current();
        $this->assertSame('text/html; charset=UTF-8', $document['content_type'][0], 'Written document does not contain extracted content type');
        $this->assertSame('HTML Stream Title', $document['title'][0], 'Written document does not contain extracted title');
        $this->assertMatchesRegularExpression('/^HTML Stream Title\s+HTML Stream Body$/', trim($document['content'][0]), 'Written document does not contain extracted result');
        $this->assertSame(['bar 3'], $document['attr_foo_3']);

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
        $this->assertSame('PDF Test', trim($response->getFile()), 'Can not extract the plain content from the PDF file');
        $this->assertArrayHasKey('stream_size', $response->getFileMetadata());

        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testhtml.html');

        $response = self::$client->extract($query);
        $this->assertMatchesRegularExpression('/^HTML Test Title\s+HTML Test Body$/', trim($response->getFile()), 'Can not extract the plain content from the HTML file');
        $this->assertArrayHasKey('stream_size', $response->getFileMetadata());

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
        $this->assertTrue(str_starts_with($response->getFile(), '<?xml version="1.0" encoding="UTF-8"?>'), 'Extracted content from the PDF file is not XML');
        $this->assertTrue(str_contains($response->getFile(), '<p>PDF Test</p>'), 'Extracted content from the PDF file not found in XML');
        $this->assertArrayHasKey('stream_size', $response->getFileMetadata());

        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'testhtml.html');

        $response = self::$client->extract($query);
        $this->assertTrue(str_starts_with($response->getFile(), '<?xml version="1.0" encoding="UTF-8"?>'), 'Extracted content from the HTML file is not XML');
        $this->assertTrue(str_contains($response->getFile(), '<title>HTML Test Title</title>'), 'Extracted title from the HTML file not found in XML');
        $this->assertTrue(str_contains($response->getFile(), '<p>HTML Test Body</p>'), 'Extracted body from the HTML file not found in XML');
        $this->assertArrayHasKey('stream_size', $response->getFileMetadata());

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
     * We don't test this with PostBigExtractRequest because we can't remove the plugin after an Exception.
     */
    public function testExtractInvalidFile()
    {
        $extract = self::$client->createExtract();
        $extract->setFile(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'nosuchfile');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Extract query file path/url invalid or not available: '.__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'nosuchfile');
        self::$client->extract($extract);
    }

    /**
     * Test the ability to execute Luke queries.
     *
     * The main purpose of this test is to check that our assumptions about which
     * ResponseParser to use for a given combination of query parameters are correct.
     * We test this with a few sanity checks on the results.
     *
     * Exhaustive tests for all fields of every response type are part of the unit tests.
     *
     * We don't test on SolrCloud because it returns the Luke result for a single shard,
     * not the entire index. That makes some expected values unpredictable. We'd also
     * need to know the correct shard to query for a specific document.
     *
     * @group skip_for_solr_cloud
     */
    public function testLuke()
    {
        $luke = self::$client->createLuke();

        // SHOW_INDEX has the simplest response, which is also included in all other responses
        $luke->setShow(LukeQuery::SHOW_INDEX);
        $resultShowIndex = self::$client->luke($luke);

        /** @var LukeIndexResult $index */
        $index = $resultShowIndex->getIndex();

        // there are 32 docs in techproducts, other tests may have added to that number
        $this->assertGreaterThanOrEqual(32, $index->getMaxDoc());

        // this is the only response that doesn't include an 'info' key
        $this->assertNull($resultShowIndex->getInfo());

        // default behaviour without 'show' is the same as ...
        $luke->setOptions(['show' => null]);
        $resultDefault = self::$client->luke($luke);

        // ... SHOW_DOC without specifying an id or docId ...
        $luke->setShow(LukeQuery::SHOW_DOC);
        $resultShowDoc = self::$client->luke($luke);

        // ... which behaves exactly like SHOW_ALL
        $luke->setShow(LukeQuery::SHOW_ALL);
        $resultShowAll = self::$client->luke($luke);

        $this->assertSame($resultShowAll->getData(), $resultDefault->getData());
        $this->assertSame($resultShowAll->getData(), $resultShowDoc->getData());

        $this->assertEquals($index, $resultShowAll->getIndex());
        $this->assertNotNull($resultShowAll->getInfo());

        /** @var LukeFieldInfo[] $fields */
        $fields = $resultShowAll->getFields();

        // this result includes information on all fields, with index-flags, without expensive details
        $this->assertGreaterThan(2, \count($fields));
        $this->assertSame('id', $fields['id']->getName());
        $this->assertTrue($fields['id']->getIndex()->isIndexed());
        $this->assertSame('cat', $fields['cat']->getName());
        $this->assertNull($fields['cat']->getDistinct());
        $this->assertNull($fields['cat']->getTopTerms());
        $this->assertNull($fields['cat']->getHistogram());

        // "all fields" can be requested explicitly to include expensive detailed information
        $luke->setFields('*');
        $resultShowAllFields = self::$client->luke($luke);

        /** @var LukeFieldInfo[] $fieldsAll */
        $fieldsAll = $resultShowAllFields->getFields();

        // this results includes the same fields as the previous one, with expensive details
        $this->assertEquals(array_keys($fields), array_keys($fieldsAll));
        $this->assertSame('id', $fieldsAll['id']->getName());
        $this->assertTrue($fieldsAll['id']->getIndex()->isIndexed());
        $this->assertSame('cat', $fieldsAll['cat']->getName());
        $this->assertIsInt($fieldsAll['cat']->getDistinct());
        $this->assertIsArray($fieldsAll['cat']->getTopTerms());
        $this->assertIsArray($fieldsAll['cat']->getHistogram());

        // fields included with SHOW_ALL can be limited
        $luke->setFields('id,cat');
        $resultShowAllFields = self::$client->luke($luke);

        /** @var LukeFieldInfo[] $fieldsFields */
        $fieldsFields = $resultShowAllFields->getFields();

        // this result only includes the requested fields, with expensive details
        $this->assertCount(2, $fieldsFields);
        $this->assertEquals($fieldsAll['id'], $fieldsFields['id']);
        $this->assertEquals($fieldsAll['cat'], $fieldsFields['cat']);

        // the number of top terms can be changed from the default
        $luke->setNumTerms(2);
        $resultShowAllFields = self::$client->luke($luke);

        /** @var LukeFieldInfo[] $fieldsFields */
        $fieldsNumTerms = $resultShowAllFields->getFields();

        $this->assertCount(2, $fieldsNumTerms['cat']->getTopTerms());
        $this->assertSame(
            \array_slice($fieldsFields['cat']->getTopTerms(), 0, 2),
            $fieldsNumTerms['cat']->getTopTerms()
        );

        // returning index-flags has non-zero cost and can be disabled for SHOW_ALL
        $luke->setIncludeIndexFieldFlags(false);
        $resultShowAllFields = self::$client->luke($luke);
        $this->assertNull($resultShowAllFields->getFields()['id']->getIndex());

        // if fields are set, 'show' can be omitted
        $luke->setOptions(['show' => null]);
        $resultDefaultFields = self::$client->luke($luke);

        // and if fields are set, SHOW_DOC is ignored
        $luke->setShow(LukeQuery::SHOW_DOC);
        $resultShowDocFields = self::$client->luke($luke);

        $this->assertSame($resultShowAllFields->getData(), $resultDefaultFields->getData());
        $this->assertSame($resultShowAllFields->getData(), $resultShowDocFields->getData());

        // SHOW_SCHEMA has the most complex ResponseParser
        $luke->setShow(LukeQuery::SHOW_SCHEMA);
        $resultShowSchema = self::$client->luke($luke);

        $this->assertEquals($index, $resultShowSchema->getIndex());
        $this->assertNotNull($resultShowSchema->getInfo());

        /** @var LukeSchemaResult $schema */
        $schema = $resultShowSchema->getSchema();

        $id = $schema->getField('id');
        $this->assertSame('id', $id->getName());
        $this->assertTrue($id->getFlags()->isIndexed());
        $this->assertTrue($id->isUniqueKey());

        $manu = $schema->getField('manu');
        $manuExact = $schema->getField('manu_exact');
        $this->assertContains($manuExact, $manu->getCopyDests());
        $this->assertContains($manu, $manuExact->getCopySources());

        $price = $schema->getField('price');
        $price_c = $price->getCopyDests()[0];
        $_c = $schema->getDynamicField('*_c');
        $this->assertContains($price, $price_c->getCopySources());
        $this->assertSame($_c, $price_c->getDynamicBase());

        $this->assertSame($id, $schema->getUniqueKeyField());

        $this->assertSame(
            'org.apache.solr.search.similarities.SchemaSimilarityFactory$SchemaSimilarity',
            $schema->getSimilarity()->getClassName()
        );

        $string = $schema->getType('string');
        $this->assertSame($string, $id->getType());
        $this->assertContains($id, $string->getFields());
        $this->assertSame('org.apache.solr.schema.StrField', $string->getClassName());
        $this->assertFalse($string->isTokenized());

        $currency = $schema->getType('currency');
        $this->assertSame($currency, $_c->getType());
        $this->assertContains($_c, $currency->getFields());

        // SHOW_DOC with an id or docId has a simpler ResponseParser, but with a SimpleOrderedMap quirk
        $luke->setShow(LukeQuery::SHOW_DOC);
        $luke->setId('9885A004');
        $resultShowDocId = self::$client->luke($luke);

        $this->assertEquals($index, $resultShowDocId->getIndex());
        $this->assertNotNull($resultShowDocId->getInfo());

        /** @var LukeDocInfo $doc */
        $doc = $resultShowDocId->getDoc();

        // Lucene data is a SimpleOrderedMap in Solr and lists a multiValued field once per value
        /** @var LukeDocFieldInfo[] $lucene */
        $lucene = $doc->getLucene();
        $this->assertSame('id', $lucene[0]->getName());
        $this->assertTrue($lucene[0]->getSchema()->isIndexed());
        $this->assertTrue($lucene[0]->getFlags()->isIndexed());
        $this->assertSame('cat', $lucene[4]->getName());
        $this->assertSame('cat', $lucene[5]->getName());
        $this->assertSame('electronics', $lucene[4]->getValue());
        $this->assertSame('camera', $lucene[5]->getValue());

        /** @var Document $solr */
        $solr = $doc->getSolr();
        $this->assertSame(
            [
                'electronics',
                'camera',
            ],
            $solr->cat
        );

        // the corresponding docId retrieves details about the same document
        $luke->setOptions(['id' => null]);
        $luke->setDocId($doc->getDocId());
        $resultShowDocDocId = self::$client->luke($luke);

        $this->assertEquals($doc, $resultShowDocDocId->getDoc());
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
                'handler' => 9 <= self::$solrVersion ? 'node/logging/levels' : 'node/logging',
            ]);
            $response = self::$client->execute($query);
            $this->assertArrayHasKey('levels', $response->getData());
            $this->assertArrayHasKey('loggers', $response->getData());

            $query = self::$client->createApi([
                'version' => Request::API_V2,
                'handler' => 'node/_introspect',
            ]);
            $response = self::$client->execute($query);
            $this->assertSame('This response format is experimental.  It is likely to change in the future.', $response->getWarning());
        } else {
            $this->markTestSkipped('V2 API requires Solr 7.');
        }
    }

    /**
     * Update queries with a different input encoding than the default UTF-8
     * aren't supported by the JSON request format.
     *
     * @see https://www.rfc-editor.org/rfc/rfc8259#section-8.1
     */
    public function testInputEncoding()
    {
        $select = self::$client->createSelect();
        $select->addSort('id', $select::SORT_ASC);
        $select->setFields('id,name,price');

        // input encoding: UTF-8 (default)
        $update = self::$client->createUpdate();
        $update->setRequestFormat(UpdateQuery::REQUEST_FORMAT_XML);
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
        $select->setQuery('cat:'.iconv('UTF-8', 'ISO-8859-1', 'áéíóú'));
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
        $update->setRequestFormat(UpdateQuery::REQUEST_FORMAT_XML);
        $update->setInputEncoding('ISO-8859-1');
        $doc = $update->createDocument();
        $doc->setField('id', iconv('UTF-8', 'ISO-8859-1', 'solarium-test-2'));
        $doc->setField('name', iconv('UTF-8', 'ISO-8859-1', 'Sølåríùm Tëst 2'));
        $doc->setField('cat', [iconv('UTF-8', 'ISO-8859-1', 'solarium-test'), iconv('UTF-8', 'ISO-8859-1', 'áéíóú')]);
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
        $update->setRequestFormat(UpdateQuery::REQUEST_FORMAT_XML);
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

    public function testEventDispatching()
    {
        $eventTimer = new EventTimer();
        self::$client->registerPlugin('eventtimer', $eventTimer);

        $query = self::$client->createSelect();
        self::$client->select($query);

        $expectedEvents = [
            'preCreateQuery',
            'postCreateQuery',
            'preExecute',
            'preCreateRequest',
            'postCreateRequest',
            'preExecuteRequest',
            'postExecuteRequest',
            'preCreateResult',
            'postCreateResult',
            'postExecute',
        ];
        $this->assertSame($expectedEvents, array_column($eventTimer->getLog(), 'event'));

        // ParallelExecution only works with Curl
        if (self::$client->getAdapter() instanceof Curl) {
            $eventTimer->reset();

            $parallel = self::$client->getPlugin('parallelexecution');
            $query = self::$client->createSelect();
            $parallel->addQuery('query', $query);
            $parallel->execute();

            array_splice($expectedEvents, 6, 0, ['parallelExecuteStart']);
            array_splice($expectedEvents, 7, 0, ['parallelExecuteEnd']);
            $this->assertSame($expectedEvents, array_column($eventTimer->getLog(), 'event'));

            self::$client->removePlugin('parallelexecution');
        }

        self::$client->removePlugin('eventtimer');
    }

    /**
     * Test the various return types that are valid for custom query classes that
     * override the {@see \Solarium\Component\QueryTrait::setQuery()} method.
     *
     * If this test throws a fatal error, the return type of the parent might no
     * longer be backward compatible with existing code that overrides it.
     *
     * @see https://github.com/solariumphp/solarium/issues/1097
     *
     * @dataProvider customQueryClassProvider
     */
    public function testCustomQueryClassSetQueryReturnType(string $queryClass)
    {
        $query = new $queryClass();
        $this->assertInstanceOf(QueryInterface::class, $query->setQuery('*:*'));
    }

    public function customQueryClassProvider(): array
    {
        return [
            [CustomStaticQuery::class],
            [CustomSelfQuery::class],
            [CustomQueryInterfaceQuery::class],
        ];
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
     * @return RequestBuilderInterface
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new NonControlCharFilteringUpdateRequestBuilder();
    }
}

class NonControlCharFilteringUpdateRequestBuilder extends XmlUpdateRequestBuilder
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
