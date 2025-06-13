<?php

namespace Solarium\Tests\QueryType\Select;

use PHPUnit\Framework\TestCase;
use Solarium\Component\AbstractComponent;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Select\Query\FilterQuery;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\RequestBuilder;

class RequestBuilderTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var RequestBuilder
     */
    protected $builder;

    public function setUp(): void
    {
        $this->query = new Query();
        $this->builder = new RequestBuilder();
    }

    public function testGetMethod(): void
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
    }

    public function testSelectUrlWithDefaultValues(): void
    {
        $request = $this->builder->build($this->query);

        $this->assertNull($request->getRawData());
        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score',
            urldecode($request->getUri())
        );
    }

    public function testSelectUrlWithSort(): void
    {
        $this->query->addSort('id', Query::SORT_ASC);
        $this->query->addSort('name', Query::SORT_DESC);
        $request = $this->builder->build($this->query);

        $this->assertNull($request->getRawData());

        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score&sort=id asc,name desc',
            urldecode($request->getUri())
        );
    }

    public function testSelectUrlWithQueryDefaultFieldAndOperator(): void
    {
        $this->query->setQueryDefaultField('mydefault');
        $this->query->setQueryDefaultOperator(Query::QUERY_OPERATOR_AND);
        $request = $this->builder->build($this->query);

        $this->assertNull($request->getRawData());

        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score&q.op=AND&df=mydefault',
            urldecode($request->getUri())
        );
    }

    public function testSelectUrlWithSortAndFilters(): void
    {
        $this->query->addSort('id', Query::SORT_ASC);
        $this->query->addSort('name', Query::SORT_DESC);
        $this->query->addFilterQuery(new FilterQuery(['key' => 'f1', 'query' => 'published:true']));
        $this->query->addFilterQuery(
            new FilterQuery(['key' => 'f2', 'local_tag' => ['t1', 't2'], 'query' => 'category:23'])
        );
        $request = $this->builder->build($this->query);

        $this->assertNull($request->getRawData());

        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score&sort=id asc,name desc'.
            '&fq=published:true&fq={!tag=t1,t2}category:23',
            urldecode($request->getUri())
        );
    }

    public function testSelectUrlWithCachedFilters(): void
    {
        $this->query->addFilterQuery(
            new FilterQuery(['key' => 'f1', 'query' => 'published:true', 'local_cache' => false, 'local_cost' => 95])
        );
        $this->query->addFilterQuery(
            new FilterQuery(['key' => 'f2', 'local_tag' => ['t1', 't2'], 'query' => 'category:23', 'local_cache' => true])
        );
        $request = $this->builder->build($this->query);

        $this->assertNull($request->getRawData());

        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score'.
            '&fq={!cache=false cost=95}published:true&fq={!tag=t1,t2 cache=true}category:23',
            urldecode($request->getUri())
        );
    }

    public function testWithComponentNoBuilder(): void
    {
        $this->builder->build($this->query);
        $this->query->registerComponentType('testcomponent', __NAMESPACE__.'\\TestDummyComponent');
        $this->query->getComponent('testcomponent', true);

        $this->expectException(\TypeError::class);
        $this->builder->build($this->query);
    }

    public function testWithComponent(): void
    {
        $this->query->getDisMax();
        $request = $this->builder->build($this->query);

        $this->assertSame('dismax', $request->getParam('defType'));
    }

    public function testWithEdismaxComponent(): void
    {
        $this->query->getEDisMax();
        $request = $this->builder->build($this->query);

        $this->assertSame('edismax', $request->getParam('defType'));
    }

    public function testWithSuggesterComponent(): void
    {
        $suggester = $this->query->getSuggester();
        $suggester->setDictionary(['dict1', 'dict2']);

        $request = $this->builder->build($this->query);

        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score&suggest=true&suggest.dictionary=dict1&suggest.dictionary=dict2',
            urldecode($request->getUri())
        );
    }

    public function testWithCancellableQuery(): void
    {
        $this->query->setCanCancel(true);
        $this->query->setQueryUuid('foobar');
        $request = $this->builder->build($this->query);

        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&canCancel=true&queryUUID=foobar&fl=*,score',
            urldecode($request->getUri())
        );
    }

    public function testWithTags(): void
    {
        $this->query->setTags(['t1', 't2']);
        $this->query->setQuery('cat:1');
        $request = $this->builder->build($this->query);

        $this->assertSame('{!tag=t1,t2}cat:1', $request->getParam('q'));
    }

    public function testWithExecutionLimits(): void
    {
        $this->query->setPartialResults(true);
        $this->query->setTimeAllowed(1000);
        $this->query->setCpuAllowed(500);
        $this->query->setMemAllowed(2.5);
        $request = $this->builder->build($this->query);

        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score&partialResults=true&timeAllowed=1000&cpuAllowed=500&memAllowed=2.5',
            urldecode($request->getUri())
        );
    }

    public function testWithSegmentTerminateEarly(): void
    {
        $this->query->setSegmentTerminateEarly(true);
        $request = $this->builder->build($this->query);

        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score&segmentTerminateEarly=true',
            urldecode($request->getUri())
        );
    }

    public function testWithMultiThreaded(): void
    {
        $this->query->setMultiThreaded(true);
        $request = $this->builder->build($this->query);

        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score&multiThreaded=true',
            urldecode($request->getUri())
        );
    }

    public function testWithCursorMark(): void
    {
        $this->query->setCursorMark('*');
        $request = $this->builder->build($this->query);

        $this->assertSame('*', $request->getParam('cursorMark'));
    }

    public function testWithSplitOnWhitespace(): void
    {
        $this->query->setSplitOnWhitespace(false);
        $request = $this->builder->build($this->query);

        $this->assertSame('false', $request->getParam('sow'));
    }
}

class TestDummyComponent extends AbstractComponent
{
    public function getType(): string
    {
        return 'testcomponent';
    }

    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return null;
    }

    public function getResponseParser(): ?ComponentParserInterface
    {
        return null;
    }
}
