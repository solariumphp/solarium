<?php

namespace Solarium\Tests\QueryType\Select;

use PHPUnit\Framework\TestCase;
use Solarium\Component\AbstractComponent;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Select\Query\FilterQuery;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\RequestBuilder as RequestBuilder;

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

    public function setUp()
    {
        $this->query = new Query();
        $this->builder = new RequestBuilder();
    }

    public function testGetMethod()
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
    }

    public function testSelectUrlWithDefaultValues()
    {
        $request = $this->builder->build($this->query);

        $this->assertNull($request->getRawData());
        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score',
            urldecode($request->getUri())
        );
    }

    public function testSelectUrlWithSort()
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

    public function testSelectUrlWithQueryDefaultFieldAndOperator()
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

    public function testSelectUrlWithSortAndFilters()
    {
        $this->query->addSort('id', Query::SORT_ASC);
        $this->query->addSort('name', Query::SORT_DESC);
        $this->query->addFilterQuery(new FilterQuery(['key' => 'f1', 'query' => 'published:true']));
        $this->query->addFilterQuery(
            new FilterQuery(['key' => 'f2', 'tag' => ['t1', 't2'], 'query' => 'category:23'])
        );
        $request = $this->builder->build($this->query);

        $this->assertNull($request->getRawData());

        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score&sort=id asc,name desc'.
            '&fq=published:true&fq={!tag=t1,t2}category:23',
            urldecode($request->getUri())
        );
    }

    public function testWithComponentNoBuilder()
    {
        $request = $this->builder->build($this->query);

        $this->query->registerComponentType('testcomponent', __NAMESPACE__.'\\TestDummyComponent');
        $this->query->getComponent('testcomponent', true);

        $requestWithNoBuilderComponent = $this->builder->build($this->query);

        $this->assertEquals($request, $requestWithNoBuilderComponent);
    }

    public function testWithComponent()
    {
        $this->query->getDisMax();
        $request = $this->builder->build($this->query);

        $this->assertSame('dismax', $request->getParam('defType'));
    }

    public function testWithEdismaxComponent()
    {
        $this->query->getEDisMax();
        $request = $this->builder->build($this->query);

        $this->assertSame('edismax', $request->getParam('defType'));
    }

    public function testWithSuggesterComponent()
    {
        $suggester = $this->query->getSuggester();
        $suggester->setDictionary(['dict1', 'dict2']);

        $request = $this->builder->build($this->query);

        $this->assertSame(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score&suggest=true&suggest.dictionary=dict1&suggest.dictionary=dict2',
            urldecode($request->getUri())
        );
    }

    public function testWithTags()
    {
        $this->query->setTags(['t1', 't2']);
        $this->query->setQuery('cat:1');
        $request = $this->builder->build($this->query);

        $this->assertSame('{!tag=t1,t2}cat:1', $request->getParam('q'));
    }

    public function testWithCursormark()
    {
        $this->query->setCursormark('*');
        $request = $this->builder->build($this->query);

        $this->assertSame('*', $request->getParam('cursorMark'));
    }

    public function testWithSplitOnWhitespace()
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

    public function getRequestBuilder()
    {
        return null;
    }

    public function getResponseParser()
    {
        return null;
    }
}
