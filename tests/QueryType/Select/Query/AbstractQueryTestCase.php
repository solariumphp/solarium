<?php

namespace Solarium\Tests\QueryType\Select\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Analytics\Analytics;
use Solarium\Component\Debug;
use Solarium\Component\DisMax;
use Solarium\Component\DistributedSearch;
use Solarium\Component\Facet\Field as FieldFacet;
use Solarium\Component\Facet\Query as QueryFacet;
use Solarium\Component\FacetSet;
use Solarium\Component\Grouping;
use Solarium\Component\Highlighting\Highlighting;
use Solarium\Component\MoreLikeThis;
use Solarium\Component\QueryElevation;
use Solarium\Component\ReRankQuery;
use Solarium\Component\Spatial;
use Solarium\Component\Spellcheck;
use Solarium\Component\Stats\Stats;
use Solarium\Component\Suggester;
use Solarium\Component\TermVector;
use Solarium\Core\Client\Client;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;
use Solarium\QueryType\Select\Query\FilterQuery;
use Solarium\QueryType\Select\Query\Query;

abstract class AbstractQueryTestCase extends TestCase
{
    protected Query $query;

    public function testGetType(): void
    {
        $this->assertSame(Client::QUERY_SELECT, $this->query->getType());
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\ResponseParser',
            $this->query->getResponseParser()
        );
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\RequestBuilder',
            $this->query->getRequestBuilder()
        );
    }

    public function testSetAndGetResultClass(): void
    {
        $this->query->setResultClass('MyResult');
        $this->assertSame('MyResult', $this->query->getResultClass());
    }

    public function testSetAndGetDocumentClass(): void
    {
        $this->query->setDocumentClass('MyDocument');
        $this->assertSame('MyDocument', $this->query->getDocumentClass());
    }

    public function testSetAndGetQueryWithTrim(): void
    {
        $this->query->setQuery(' *:* ');
        $this->assertSame('*:*', $this->query->getQuery());
    }

    public function testSetAndGetQueryWithBind(): void
    {
        $this->query->setQuery('id:%1%', [678]);
        $this->assertSame('id:678', $this->query->getQuery());
    }

    public function testSetAndGetQueryDefaultOperator(): void
    {
        $value = Query::QUERY_OPERATOR_AND;

        $this->query->setQueryDefaultOperator($value);
        $this->assertSame($value, $this->query->getQueryDefaultOperator());
    }

    public function testSetAndGetQueryDefaultField(): void
    {
        $value = 'mydefault';

        $this->query->setQueryDefaultField($value);
        $this->assertSame($value, $this->query->getQueryDefaultField());
    }

    public function testSetAndGetStart(): void
    {
        $this->query->setStart(234);
        $this->assertSame(234, $this->query->getStart());
    }

    public function testSetAndGetRows(): void
    {
        $this->query->setRows(100);
        $this->assertSame(100, $this->query->getRows());
    }

    public function testGetDefaultCanCancel(): void
    {
        $this->assertNull($this->query->getCanCancel());
    }

    public function testSetAndGetCanCancel(): void
    {
        $this->query->setCanCancel(true);
        $this->assertTrue($this->query->getCanCancel());
    }

    public function testGetDefaultQueryUuid(): void
    {
        $this->assertNull($this->query->getQueryUuid());
    }

    public function testSetAndGetQueryUuid(): void
    {
        $this->query->setQueryUuid('foobar');
        $this->assertSame('foobar', $this->query->getQueryUuid());
    }

    public function testAddField(): void
    {
        $expectedFields = $this->query->getFields();
        $expectedFields[] = 'newfield';
        $this->query->addField('newfield');
        $this->assertSame($expectedFields, $this->query->getFields());
    }

    public function testClearFields(): void
    {
        $this->query->addField('newfield');
        $this->query->clearFields();
        $this->assertSame([], $this->query->getFields());
    }

    public function testAddFields(): void
    {
        $fields = ['field1', 'field2'];

        $this->query->clearFields();
        $this->query->addFields($fields);
        $this->assertSame($fields, $this->query->getFields());
    }

    public function testAddFieldsAsStringWithTrim(): void
    {
        $this->query->clearFields();
        $this->query->addFields('field1, field2');
        $this->assertSame(['field1', 'field2'], $this->query->getFields());
    }

    public function testRemoveField(): void
    {
        $this->query->clearFields();
        $this->query->addFields(['field1', 'field2']);
        $this->query->removeField('field1');
        $this->assertSame(['field2'], $this->query->getFields());
    }

    public function testSetFields(): void
    {
        $this->query->clearFields();
        $this->query->addFields(['field1', 'field2']);
        $this->query->setFields(['field3', 'field4']);
        $this->assertSame(['field3', 'field4'], $this->query->getFields());
    }

    public function testAddSort(): void
    {
        $this->query->addSort('field1', Query::SORT_DESC);
        $this->assertSame(
            ['field1' => Query::SORT_DESC],
            $this->query->getSorts()
        );
    }

    public function testAddSorts(): void
    {
        $sorts = [
            'field1' => Query::SORT_DESC,
            'field2' => Query::SORT_ASC,
        ];

        $this->query->addSorts($sorts);
        $this->assertSame(
            $sorts,
            $this->query->getSorts()
        );
    }

    public function testRemoveSort(): void
    {
        $sorts = [
            'field1' => Query::SORT_DESC,
            'field2' => Query::SORT_ASC,
        ];

        $this->query->addSorts($sorts);
        $this->query->removeSort('field1');
        $this->assertSame(
            ['field2' => Query::SORT_ASC],
            $this->query->getSorts()
        );
    }

    public function testRemoveInvalidSort(): void
    {
        $sorts = [
            'field1' => Query::SORT_DESC,
            'field2' => Query::SORT_ASC,
        ];

        $this->query->addSorts($sorts);
        $this->query->removeSort('invalidfield'); // continue silently
        $this->assertSame(
            $sorts,
            $this->query->getSorts()
        );
    }

    public function testClearSorts(): void
    {
        $sorts = [
            'field1' => Query::SORT_DESC,
            'field2' => Query::SORT_ASC,
        ];

        $this->query->addSorts($sorts);
        $this->query->clearSorts();
        $this->assertSame(
            [],
            $this->query->getSorts()
        );
    }

    public function testSetSorts(): void
    {
        $sorts = [
            'field1' => Query::SORT_DESC,
            'field2' => Query::SORT_ASC,
        ];

        $this->query->addSorts($sorts);
        $this->query->setSorts(['field3' => Query::SORT_ASC]);
        $this->assertSame(
            ['field3' => Query::SORT_ASC],
            $this->query->getSorts()
        );
    }

    public function testAddAndGetFilterQuery(): void
    {
        $fq = new FilterQuery();
        $fq->setKey('fq1')->setQuery('category:1');
        $this->query->addFilterQuery($fq);

        $this->assertSame(
            $fq,
            $this->query->getFilterQuery('fq1')
        );
    }

    public function testAddAndGetFilterQueryWithKey(): void
    {
        $key = 'fq1';

        $fq = $this->query->createFilterQuery($key);
        $fq->setQuery('category:1');

        $this->assertSame(
            $key,
            $fq->getKey()
        );

        $this->assertSame(
            $fq,
            $this->query->getFilterQuery('fq1')
        );
    }

    public function testAddFilterQueryWithoutKey(): void
    {
        $fq = new FilterQuery();
        $fq->setQuery('category:1');

        $this->expectException(InvalidArgumentException::class);
        $this->query->addFilterQuery($fq);
    }

    public function testAddFilterQueryWithEmptyKey(): void
    {
        $fq = new FilterQuery();
        $fq->setKey('')->setQuery('category:1');

        $this->expectException(InvalidArgumentException::class);
        $this->query->addFilterQuery($fq);
    }

    public function testAddFilterQueryWithUsedKey(): void
    {
        $fq1 = new FilterQuery();
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery();
        $fq2->setKey('fq1')->setQuery('category:2');

        $this->query->addFilterQuery($fq1);
        $this->expectException(InvalidArgumentException::class);
        $this->query->addFilterQuery($fq2);
    }

    public function testGetInvalidFilterQuery(): void
    {
        $this->assertNull(
            $this->query->getFilterQuery('invalidtag')
        );
    }

    public function testAddFilterQueries(): void
    {
        $fq1 = new FilterQuery();
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery();
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = ['fq1' => $fq1, 'fq2' => $fq2];

        $this->query->addFilterQueries($filterQueries);
        $this->assertSame(
            $filterQueries,
            $this->query->getFilterQueries()
        );
    }

    public function testRemoveFilterQuery(): void
    {
        $fq1 = new FilterQuery();
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery();
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = [$fq1, $fq2];

        $this->query->addFilterQueries($filterQueries);
        $this->query->removeFilterQuery('fq1');
        $this->assertSame(
            ['fq2' => $fq2],
            $this->query->getFilterQueries()
        );
    }

    public function testRemoveFilterQueryWithObjectInput(): void
    {
        $fq1 = new FilterQuery();
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery();
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = [$fq1, $fq2];

        $this->query->addFilterQueries($filterQueries);
        $this->query->removeFilterQuery($fq1);
        $this->assertSame(
            ['fq2' => $fq2],
            $this->query->getFilterQueries()
        );
    }

    public function testRemoveInvalidFilterQuery(): void
    {
        $fq1 = new FilterQuery();
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery();
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = ['fq1' => $fq1, 'fq2' => $fq2];

        $this->query->addFilterQueries($filterQueries);
        $this->query->removeFilterQuery('fq3'); // continue silently
        $this->assertSame(
            $filterQueries,
            $this->query->getFilterQueries()
        );
    }

    public function testClearFilterQueries(): void
    {
        $fq1 = new FilterQuery();
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery();
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = [$fq1, $fq2];

        $this->query->addFilterQueries($filterQueries);
        $this->query->clearFilterQueries();
        $this->assertSame(
            [],
            $this->query->getFilterQueries()
        );
    }

    public function testSetFilterQueries(): void
    {
        $fq1 = new FilterQuery();
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery();
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries1 = ['fq1' => $fq1, 'fq2' => $fq2];

        $this->query->addFilterQueries($filterQueries1);

        $fq3 = new FilterQuery();
        $fq3->setKey('fq3')->setQuery('category:3');

        $fq4 = new FilterQuery();
        $fq4->setKey('fq4')->setQuery('category:4');

        $filterQueries2 = ['fq3' => $fq3, 'fq4' => $fq4];

        $this->query->setFilterQueries($filterQueries2);

        $this->assertSame(
            $filterQueries2,
            $this->query->getFilterQueries()
        );
    }

    public function testConfigMode(): void
    {
        $config = [
            'query' => 'text:mykeyword',
            'sort' => ['score' => 'asc'],
            'fields' => ['id', 'title', 'category'],
            'rows' => 100,
            'start' => 200,
            'filterquery' => [
                ['key' => 'pub', 'local_tag' => ['pub'], 'query' => 'published:true'],
                'online' => ['local_tag' => 'onl', 'query' => 'online:true'],
            ],
            'component' => [
                'facetset' => [
                    'facet' => [
                        ['type' => 'field', 'local_key' => 'categories', 'field' => 'category'],
                        'category13' => ['type' => 'query', 'query' => 'category:13'],
                    ],
                ],
            ],
            'resultclass' => 'MyResultClass',
            'documentclass' => 'MyDocumentClass',
            'local_tag' => ['t1', 't2'],
            'cursormark' => '*',
            'splitonwhitespace' => false,
        ];
        $query = new Query($config);

        $this->assertSame($config['query'], $query->getQuery());
        $this->assertSame($config['sort'], $query->getSorts());
        $this->assertSame($config['fields'], $query->getFields());
        $this->assertSame($config['rows'], $query->getRows());
        $this->assertSame($config['start'], $query->getStart());
        $this->assertSame($config['documentclass'], $query->getDocumentClass());
        $this->assertSame($config['resultclass'], $query->getResultClass());
        $this->assertSame($config['cursormark'], $query->getCursorMark());
        $this->assertFalse($query->getSplitOnWhitespace());
        $this->assertSame('published:true', $query->getFilterQuery('pub')->getQuery());
        $this->assertSame('online:true', $query->getFilterQuery('online')->getQuery());

        $facets = $query->getFacetSet()->getFacets();
        $this->assertInstanceOf(
            FieldFacet::class,
            $facets['categories']
        );
        $this->assertSame(
            'category',
            $facets['categories']->getField()
        );
        $this->assertInstanceOf(
            QueryFacet::class,
            $facets['category13']
        );
        $this->assertSame(
            'category:13',
            $facets['category13']->getQuery()
        );

        $components = $query->getComponents();
        $this->assertCount(1, $components);
        $this->assertInstanceOf(FacetSet::class, array_pop($components));
        $this->assertSame(['t1', 't2'], $query->getLocalParameters()->getTags());
    }

    public function testConfigModeWithSingleValueTag(): void
    {
        $query = new Query(['local_tag' => 't1']);
        $this->assertSame(['t1'], $query->getLocalParameters()->getTags());
    }

    public function testSetAndGetComponents(): void
    {
        $mlt = new MoreLikeThis();
        $this->query->setComponent('mlt', $mlt);

        $this->assertSame(
            ['mlt' => $mlt],
            $this->query->getComponents()
        );
    }

    public function testSetAndGetComponent(): void
    {
        $mlt = new MoreLikeThis();
        $this->query->setComponent('mlt', $mlt);

        $this->assertSame(
            $mlt,
            $this->query->getComponent('mlt')
        );
    }

    public function testSetAndGetComponentQueryInstance(): void
    {
        $mlt = new MoreLikeThis();
        $this->query->setComponent('mlt', $mlt);

        $this->assertSame(
            $this->query,
            $this->query->getComponent('mlt')->getQueryInstance()
        );
    }

    public function testGetInvalidComponent(): void
    {
        $this->assertNull(
            $this->query->getComponent('invalid')
        );
    }

    public function testGetInvalidComponentAutoload(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->query->getComponent('invalid', true);
    }

    public function testRemoveComponent(): void
    {
        $mlt = new MoreLikeThis();
        $this->query->setComponent('mlt', $mlt);

        $this->assertSame(
            ['mlt' => $mlt],
            $this->query->getComponents()
        );

        $this->query->removeComponent('mlt');

        $this->assertSame(
            [],
            $this->query->getComponents()
        );
    }

    public function testRemoveComponentWithObjectInput(): void
    {
        $mlt = new MoreLikeThis();
        $this->query->setComponent('mlt', $mlt);

        $this->assertSame(
            ['mlt' => $mlt],
            $this->query->getComponents()
        );

        $this->query->removeComponent($mlt);

        $this->assertSame(
            [],
            $this->query->getComponents()
        );
    }

    public function testGetMoreLikeThis(): void
    {
        $this->assertInstanceOf(
            MoreLikeThis::class,
            $this->query->getMoreLikeThis()
        );
    }

    public function testGetDisMax(): void
    {
        $this->assertInstanceOf(
            DisMax::class,
            $this->query->getDisMax()
        );
    }

    public function testGetHighlighting(): void
    {
        $this->assertInstanceOf(
            Highlighting::class,
            $this->query->getHighlighting()
        );
    }

    public function testGetGrouping(): void
    {
        $this->assertInstanceOf(
            Grouping::class,
            $this->query->getGrouping()
        );
    }

    public function testGetQueryElevation(): void
    {
        $this->assertInstanceOf(
            QueryElevation::class,
            $this->query->getQueryElevation()
        );
    }

    public function testRegisterComponentType(): void
    {
        $components = $this->query->getComponentTypes();
        $components['mykey'] = 'mycomponent';

        $this->query->registerComponentType('mykey', 'mycomponent');

        $this->assertSame(
            $components,
            $this->query->getComponentTypes()
        );
    }

    public function testCreateFilterQuery(): void
    {
        $options = ['optionA' => 1, 'optionB' => 2];
        $fq = $this->query->createFilterQuery($options);

        // check class
        $this->assertInstanceOf(FilterQuery::class, $fq);

        // check option forwarding
        $fqOptions = $fq->getOptions();
        $this->assertSame(
            $options['optionB'],
            $fqOptions['optionB']
        );
    }

    public function testGetSpellcheck(): void
    {
        $this->assertInstanceOf(
            Spellcheck::class,
            $this->query->getSpellcheck()
        );
    }

    public function testGetSuggester(): void
    {
        $this->assertInstanceOf(
            Suggester::class,
            $this->query->getSuggester()
        );
    }

    public function testGetTermVector(): void
    {
        $this->assertInstanceOf(
            TermVector::class,
            $this->query->getTermVector()
        );
    }

    public function testGetDistributedSearch(): void
    {
        $this->assertInstanceOf(
            DistributedSearch::class,
            $this->query->getDistributedSearch()
        );
    }

    public function testGetStats(): void
    {
        $this->assertInstanceOf(
            Stats::class,
            $this->query->getStats()
        );
    }

    public function testGetDebug(): void
    {
        $this->assertInstanceOf(
            Debug::class,
            $this->query->getDebug()
        );
    }

    public function testGetSpatial(): void
    {
        $this->assertInstanceOf(
            Spatial::class,
            $this->query->getSpatial()
        );
    }

    public function testGetReRankQuery(): void
    {
        $this->assertInstanceOf(
            ReRankQuery::class,
            $this->query->getReRankQuery()
        );
    }

    public function testGetAnalytics(): void
    {
        $this->assertInstanceOf(
            Analytics::class,
            $this->query->getAnalytics()
        );
    }

    public function testAddTag(): void
    {
        $this->query->addTag('testtag');
        $this->assertSame(['testtag'], $this->query->getTags());
    }

    public function testAddTags(): void
    {
        $this->query->addTags(['t1', 't2']);
        $this->assertSame(['t1', 't2'], $this->query->getTags());
    }

    public function testRemoveTag(): void
    {
        $this->query->addTags(['t1', 't2']);
        $this->query->removeTag('t1');
        $this->assertSame(['t2'], $this->query->getTags());
    }

    public function testClearTags(): void
    {
        $this->query->addTags(['t1', 't2']);
        $this->query->clearTags();
        $this->assertSame([], $this->query->getTags());
    }

    public function testSetTags(): void
    {
        $this->query->addTags(['t1', 't2']);
        $this->query->setTags(['t3', 't4']);
        $this->assertSame(['t3', 't4'], $this->query->getTags());
    }

    public function testGetDefaultPartialResults(): void
    {
        $this->assertNull($this->query->getPartialResults());
    }

    public function testSetAndGetPartialResults(): void
    {
        $this->query->setPartialResults(true);
        $this->assertTrue($this->query->getPartialResults());
    }

    public function testGetDefaultTimeAllowed(): void
    {
        $this->assertNull($this->query->getTimeAllowed());
    }

    public function testSetAndGetTimeAllowed(): void
    {
        $this->query->setTimeAllowed(1200);
        $this->assertSame(1200, $this->query->getTimeAllowed());
    }

    public function testGetDefaultCpuAllowed(): void
    {
        $this->assertNull($this->query->getCpuAllowed());
    }

    public function testSetAndGetCpuAllowed(): void
    {
        $this->query->setCpuAllowed(500);
        $this->assertSame(500, $this->query->getCpuAllowed());
    }

    public function testGetDefaultMemAllowed(): void
    {
        $this->assertNull($this->query->getMemAllowed());
    }

    public function testSetAndGetMemAllowed(): void
    {
        $this->query->setMemAllowed(2.5);
        $this->assertSame(2.5, $this->query->getMemAllowed());
    }

    public function testGetDefaultSegmentTerminateEarly(): void
    {
        $this->assertNull($this->query->getSegmentTerminateEarly());
    }

    public function testSetAndGetSegmentTerminateEarly(): void
    {
        $this->query->setSegmentTerminateEarly(true);
        $this->assertTrue($this->query->getSegmentTerminateEarly());
    }

    public function testGetDefaultMultiThreaded(): void
    {
        $this->assertNull($this->query->getMultiThreaded());
    }

    public function testSetAndGetMultiThreaded(): void
    {
        $this->query->setMultiThreaded(true);
        $this->assertTrue($this->query->getMultiThreaded());
    }

    public function testSetCursorMark(): void
    {
        $this->query->setCursorMark('*');
        $this->assertSame('*', $this->query->getCursorMark());
    }

    public function testClearCursorMark(): void
    {
        $this->query->setCursorMark('*');
        $this->query->clearCursorMark();
        $this->assertNull($this->query->getCursorMark());
    }

    public function testSetAndGetSplitOnWhitespace(): void
    {
        $this->query->setSplitOnWhitespace(false);
        $this->assertFalse($this->query->getSplitOnWhitespace());
    }
}
