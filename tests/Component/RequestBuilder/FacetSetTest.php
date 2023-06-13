<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Field as FacetField;
use Solarium\Component\Facet\Interval as FacetInterval;
use Solarium\Component\Facet\JsonAggregation;
use Solarium\Component\Facet\JsonQuery;
use Solarium\Component\Facet\JsonRange;
use Solarium\Component\Facet\JsonTerms;
use Solarium\Component\Facet\MultiQuery as FacetMultiQuery;
use Solarium\Component\Facet\Pivot as FacetPivot;
use Solarium\Component\Facet\Query as FacetQuery;
use Solarium\Component\Facet\Range as FacetRange;
use Solarium\Component\FacetSet as Component;
use Solarium\Component\RequestBuilder\FacetSet as RequestBuilder;
use Solarium\Core\Client\Request;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\UnexpectedValueException;

class FacetSetTest extends TestCase
{
    /**
     * @var RequestBuilder
     */
    protected $builder;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Component
     */
    protected $component;

    public function setUp(): void
    {
        $this->builder = new RequestBuilder();
        $this->request = new Request();
        $this->component = new Component();
    }

    public function testBuildEmptyFacetSet()
    {
        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertEquals(
            [],
            $request->getParams()
        );
    }

    public function testBuildWithFacets()
    {
        $this->component->addFacet(new FacetField(['local_key' => 'f1', 'local_exclude' => 'e11,e12', 'local_terms' => 't1,t2', 'field' => 'owner']));
        $this->component->addFacet(new FacetQuery(['local_key' => 'f2', 'local_exclude' => 'e21,e22', 'query' => 'category:23']));
        $this->component->addFacet(
            new FacetMultiQuery(['local_key' => 'f3', 'query' => ['f4' => ['query' => 'category:40']]])
        );

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?facet.field={!key=f1 ex=e11,e12 terms=t1,t2}owner&facet.query={!key=f2 ex=e21,e22}category:23&facet.query={!key=f4}category:40&facet=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithFacetFieldWithCommaAndQuoteInTerm()
    {
        $this->component->addFacet(new FacetField(['local_key' => 'f1', 'local_terms' => ['yes\, it is', 'no\, it isn\'t'], 'field' => 'isit']));

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            "?facet.field={!key=f1 terms='yes\\, it is,no\\, it isn\\'t'}isit&facet=true",
            urldecode($request->getUri())
        );
    }

    public function testBuildWithJsonFacets()
    {
        $this->component->addFacet(new JsonTerms(['local_key' => 'f1', 'field' => 'owner']));
        $this->component->addFacet(new JsonQuery(['local_key' => 'f2', 'query' => 'category:23']));

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":{"field":"owner","type":"terms"},"f2":{"type":"query","q":"category:23"}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithJsonFacetFilterQuery()
    {
        $terms = new JsonTerms(['local_key' => 'f1', 'field' => 'owner']);
        $terms->setDomainFilterQuery('popularity:[5 TO 10]');
        $this->component->addFacet($terms);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":{"field":"owner","domain":{"filter":"popularity:[5 TO 10]"},"type":"terms"}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithJsonFacetFilterParams()
    {
        $terms = new JsonTerms(['local_key' => 'f1', 'field' => 'owner']);
        $terms->addDomainFilterParameter('myparam1');
        $terms->addDomainFilterParameter('myparam2');
        $terms->addDomainFilterParameter('myparam3');
        $terms->addDomainFilterParameter('myparam1');
        $this->component->addFacet($terms);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":{"field":"owner","domain":{"filter":[{"param":"myparam1"},{"param":"myparam2"},{"param":"myparam3"}]},"type":"terms"}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithJsonFacetFilterQueryAndParams()
    {
        $terms = new JsonTerms(['local_key' => 'f1', 'field' => 'owner']);
        $terms->setDomainFilterQuery('popularity:[5 TO 10]');
        $terms->addDomainFilterParameter('myparam1');
        $terms->addDomainFilterParameter('myparam2');
        $this->component->addFacet($terms);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":{"field":"owner","domain":{"filter":["popularity:[5 TO 10]",{"param":"myparam1"},{"param":"myparam2"}]},"type":"terms"}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithJsonFacetFilterQueryWithPlaceholders()
    {
        $terms = new JsonTerms(['local_key' => 'f1', 'field' => 'owner']);
        $terms->setDomainFilterQuery('popularity:[%1% TO %2%]', [5, 10]);
        $this->component->addFacet($terms);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":{"field":"owner","domain":{"filter":"popularity:[5 TO 10]"},"type":"terms"}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithJsonFacetFilterParamsAndQuery()
    {
        $terms = new JsonTerms(['local_key' => 'f1', 'field' => 'owner']);
        $terms->addDomainFilterParameter('myparam1');
        $terms->addDomainFilterParameter('myparam2');
        $terms->setDomainFilterQuery('popularity:[5 TO 10]');
        $this->component->addFacet($terms);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":{"field":"owner","domain":{"filter":[{"param":"myparam1"},{"param":"myparam2"},"popularity:[5 TO 10]"]},"type":"terms"}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithJsonFacetFilterParamsAndQueryOverwrite()
    {
        $terms = new JsonTerms(['local_key' => 'f1', 'field' => 'owner']);
        $terms->setDomainFilterQuery('popularity:[5 TO 10]');
        $terms->addDomainFilterParameter('myparam1');
        $terms->addDomainFilterParameter('myparam2');
        $terms->setDomainFilterQuery('popularity:[15 TO 20]');
        $this->component->addFacet($terms);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":{"field":"owner","domain":{"filter":["popularity:[15 TO 20]",{"param":"myparam1"},{"param":"myparam2"}]},"type":"terms"}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithFacetsAndJsonFacets()
    {
        $this->component->addFacet(new FacetField(['local_key' => 'f1', 'field' => 'owner']));
        $this->component->addFacet(new JsonTerms(['local_key' => 'f2', 'field' => 'customer']));
        $this->component->addFacet(new JsonQuery(['local_key' => 'f3', 'query' => 'category:23']));
        $this->component->addFacet(
            new FacetMultiQuery(['local_key' => 'f4', 'query' => ['f5' => ['query' => 'category:40']]])
        );

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?facet.field={!key=f1}owner&facet.query={!key=f5}category:40&facet=true&json.facet={"f2":{"field":"customer","type":"terms"},"f3":{"type":"query","q":"category:23"}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithAggregationFacet()
    {
        $this->component->addFacet(new JsonAggregation(['local_key' => 'f1', 'function' => 'avg(mul(price,popularity))']));

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":"avg(mul(price,popularity))"}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithNestedFacets()
    {
        $terms = new JsonTerms(['local_key' => 'f1', 'field' => 'owner']);
        // Only JSON facets could be nested.
        $this->expectException(InvalidArgumentException::class);
        $terms->addFacet(new FacetQuery(['local_key' => 'f2', 'q' => 'category:23']));
    }

    public function testBuildWithNestedJsonFacets()
    {
        $terms = new JsonTerms(['local_key' => 'f1', 'field' => 'owner']);
        $query = new JsonQuery(['local_key' => 'f2', 'query' => 'category:23']);
        $query->addFacet(new JsonAggregation(['local_key' => 'f1', 'function' => 'avg(mul(price,popularity))']));
        $query->addFacet(new JsonAggregation(['local_key' => 'f2', 'function' => 'unique(popularity)']));
        $terms->addFacet($query);
        $this->component->addFacet($terms);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":{"field":"owner","type":"terms","facet":{"f2":{"type":"query","facet":{"f1":"avg(mul(price,popularity))","f2":"unique(popularity)"},"q":"category:23"}}}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithNestedJsonFacetRemoved()
    {
        $terms = new JsonTerms(['local_key' => 'f1', 'field' => 'owner']);
        $query = new JsonQuery(['local_key' => 'f2', 'query' => 'category:23']);
        $query->addFacet(new JsonAggregation(['local_key' => 'f1', 'function' => 'avg(mul(price,popularity))']));
        $query->addFacet(new JsonAggregation(['local_key' => 'f2', 'function' => 'unique(popularity)']));
        $query->removeFacet('f1');
        $terms->addFacet($query);
        $this->component->addFacet($terms);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":{"field":"owner","type":"terms","facet":{"f2":{"type":"query","facet":{"f2":"unique(popularity)"},"q":"category:23"}}}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithNestedJsonFacetsCleared()
    {
        $terms = new JsonTerms(['local_key' => 'f1', 'field' => 'owner']);
        $query = new JsonQuery(['local_key' => 'f2', 'query' => 'category:23']);
        $query->addFacet(new JsonAggregation(['local_key' => 'f1', 'function' => 'avg(mul(price,popularity))']));
        $query->addFacet(new JsonAggregation(['local_key' => 'f2', 'function' => 'unique(popularity)']));
        $query->clearFacets();
        $terms->addFacet($query);
        $this->component->addFacet($terms);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":{"field":"owner","type":"terms","facet":{"f2":{"type":"query","q":"category:23"}}}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithRangeFacet()
    {
        $this->component->addFacet(new FacetRange(
            [
                'local_key' => 'f1',
                'field' => 'price',
                'start' => '1',
                'end' => 100,
                'gap' => 10,
                'other' => 'all',
                'include' => 'outer',
                'mincount' => 123,
            ]
        ));

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?facet.range={!key=f1}price&f.price.facet.range.start=1&f.price.facet.range.end=100&f.price.facet.range.gap=10&f.price.facet.mincount=123&f.price.facet.range.other=all&f.price.facet.range.include=outer&facet=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithJsonRangeFacet()
    {
        $this->component->addFacet(new JsonRange(
            [
                'local_key' => 'f1',
                'field' => 'price',
                'start' => '1',
                'end' => 100,
                'gap' => 10,
                'other' => 'all',
                'include' => 'outer',
                'mincount' => 123,
            ]
        ));

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?json.facet={"f1":{"field":"price","start":"1","end":100,"gap":10,"other":["all"],"include":["outer"],"mincount":123,"type":"range"}}',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithRangeFacetExcludingOptionalParams()
    {
        $this->component->addFacet(
            new FacetRange(
                [
                    'local_key' => 'f1',
                    'field' => 'price',
                    'start' => '1',
                    'end' => 100,
                    'gap' => 10,
                ]
            )
        );

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?facet.range={!key=f1}price&f.price.facet.range.start=1&f.price.facet.range.end=100&f.price.facet.range.gap=10&facet=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithRangeFacetAndPivot()
    {
        $this->component->addFacet(
            new FacetRange(
                [
                    'local_key' => 'key',
                    'local_tag' => 'r1',
                    'field' => 'manufacturedate_dt',
                    'start' => '2006-01-01T00:00:00Z',
                    'end' => 'NOW/YEAR',
                    'gap' => '+1YEAR',
                    'pivot' => ['fields' => ['cat', 'inStock'], 'local_range' => 'r1'],
                ]
            )
        );

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?facet.range={!key=key tag=r1}manufacturedate_dt&f.manufacturedate_dt.facet.range.start=2006-01-01T00:00:00Z&f.manufacturedate_dt.facet.range.end=NOW/YEAR&f.manufacturedate_dt.facet.range.gap=+1YEAR&facet.pivot={!range=r1}cat,inStock&facet=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithFacetsAndGlobalFacetSettings()
    {
        $this->component->setMissing(true);
        $this->component->setLimit(10);
        $this->component->addFacet(new FacetField(['local_key' => 'f1', 'field' => 'owner']));
        $this->component->addFacet(new FacetQuery(['local_key' => 'f2', 'query' => 'category:23']));
        $this->component->addFacet(
            new FacetMultiQuery(['local_key' => 'f3', 'query' => ['f4' => ['query' => 'category:40']]])
        );
        $request = $this->builder->buildComponent($this->component, $this->request);
        static::assertNull(
            $request->getRawData()
        );
        static::assertEquals(
            '?facet.field={!key=f1}owner&facet.query={!key=f2}category:23&facet.query={!key=f4}category:40&facet=true&facet.limit=10&facet.missing=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithFacetsAndSameFieldMultiplePrefix()
    {
        $this->component->setMissing(true);
        $this->component->setLimit(10);
        $this->component->addFacet((new FacetField(['local_key' => 'f1', 'field' => 'owner']))->setPrefix('Y'));

        // second use of field owner (with prefix)
        $this->component->addFacet((new FacetField(['local_key' => 'f2', 'field' => 'owner']))->setPrefix('X'));

        $request = $this->builder->buildComponent($this->component, $this->request);

        static::assertNull(
            $request->getRawData()
        );

        static::assertEquals(
            '?facet.field={!key=f1 facet.prefix=Y}owner&facet.field={!key=f2 facet.prefix=X}owner&facet=true&facet.limit=10&facet.missing=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildUnknownFacetType()
    {
        $this->component->addFacet(new UnknownFacet(['local_key' => 'f1', 'field' => 'owner']));
        $this->expectException(UnexpectedValueException::class);
        $request = $this->builder->buildComponent($this->component, $this->request);
        $request->getUri();
    }

    public function testBuildWithPivotFacet()
    {
        $facet = new FacetPivot(
            [
                'local_key' => 'f1',
                'fields' => 'cat,inStock',
                'pivot.mincount' => 123,
                'limit' => 12,
                'offset' => 25,
                'sort' => FacetPivot::SORT_INDEX,
                'overrequest.count' => 20,
                'overrequest.ratio' => 2.2,
            ]
        );
        $facet->addExclude('owner');
        $this->component->addFacet($facet);
        $this->component->setPivotMinCount(5);
        $this->component->setLimit(-1);
        $this->component->setOffset(10);
        $this->component->setSort(Component::SORT_COUNT);
        $this->component->setOverrequestCount(15);
        $this->component->setOverrequestRatio(1.3);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull(
            $request->getRawData()
        );

        $this->assertEquals(
            '?facet.pivot={!key=f1 ex=owner}cat,inStock&f.cat.facet.pivot.mincount=123&f.cat.facet.limit=12&f.cat.facet.offset=25&f.cat.facet.sort=index&f.cat.facet.overrequest.count=20&f.cat.facet.overrequest.ratio=2.2&f.inStock.facet.pivot.mincount=123&f.inStock.facet.limit=12&f.inStock.facet.offset=25&f.inStock.facet.sort=index&f.inStock.facet.overrequest.count=20&f.inStock.facet.overrequest.ratio=2.2&facet=true&facet.sort=count&facet.limit=-1&facet.offset=10&facet.overrequest.count=15&facet.overrequest.ratio=1.3&facet.pivot.mincount=5',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithPivotStatFacet()
    {
        $facet = new FacetPivot(
            [
                'local_key' => 'f1',
                'fields' => 'cat,inStock',
                'local_stats' => 'piv1',
            ]
        );
        $this->component->addFacet($facet);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.pivot={!key=cat,inStock stats=piv1}cat,inStock&facet=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithPrefix()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'owner',
                'prefix' => 'abc',
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setPrefix('xyz');

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}owner&f.owner.facet.prefix=abc&facet=true&facet.prefix=xyz',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithContainsSettings()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'owner',
                'contains' => 'foo',
                'containsignorecase' => true,
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setContains('bar');
        $this->component->setContainsIgnoreCase(false);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}owner&f.owner.facet.contains=foo&f.owner.facet.contains.ignoreCase=true&facet=true&facet.contains=bar&facet.contains.ignoreCase=false',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithMatches()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'cat',
                'matches' => 'co.*s',
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setMatches('comp.*');

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}cat&f.cat.facet.matches=co.*s&facet=true&facet.matches=comp.*',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithSort()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'owner',
                'sort' => FacetField::SORT_COUNT,
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setSort(Component::SORT_INDEX);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}owner&f.owner.facet.sort=count&facet=true&facet.sort=index',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithLimit()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'owner',
                'limit' => -1,
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setLimit(10);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}owner&f.owner.facet.limit=-1&facet=true&facet.limit=10',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithOffset()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'owner',
                'offset' => 20,
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setOffset(10);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}owner&f.owner.facet.offset=20&facet=true&facet.offset=10',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithMinCount()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'owner',
                'mincount' => 15,
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setMinCount(30);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}owner&f.owner.facet.mincount=15&facet=true&facet.mincount=30',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithMissing()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'owner',
                'missing' => false,
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setMissing(true);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}owner&f.owner.facet.missing=false&facet=true&facet.missing=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithMethod()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'owner',
                'method' => FacetField::METHOD_FCS,
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setMethod(Component::METHOD_FC);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}owner&f.owner.facet.method=fcs&facet=true&facet.method=fc',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithEnumCacheMinDf()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'cat',
                'enum.cache.minDf' => 15,
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setMethod(Component::METHOD_ENUM);
        $this->component->setEnumCacheMinimumDocumentFrequency(25);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}cat&f.cat.facet.enum.cache.minDf=15&facet=true&facet.method=enum&facet.enum.cache.minDf=25',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithExists()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'owner',
                'exists' => true,
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setExists(false);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}owner&f.owner.facet.exists=true&facet=true&facet.exists=false',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithExcludeTerms()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'cat',
                'excludeTerms' => 'music,electronics',
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setExcludeTerms('hard drive');

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}cat&f.cat.facet.excludeTerms=music,electronics&facet=true&facet.excludeTerms=hard drive',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithOverrequestSettings()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'owner',
                'overrequest.count' => 20,
                'overrequest.ratio' => 2.2,
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setOverrequestCount(15);
        $this->component->setOverrequestRatio(1.3);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}owner&f.owner.facet.overrequest.count=20&f.owner.facet.overrequest.ratio=2.2&facet=true&facet.overrequest.count=15&facet.overrequest.ratio=1.3',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithThreads()
    {
        $facet = new FacetField(
            [
                'local_key' => 'f1',
                'field' => 'owner',
                'threads' => -1,
            ]
        );
        $this->component->addFacet($facet);
        $this->component->setThreads(42);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.field={!key=f1}owner&f.owner.facet.threads=-1&facet=true&facet.threads=42',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithIntervalFacet()
    {
        $facet = new FacetInterval(
            [
                'local_key' => 'f1',
                'field' => 'cat',
                'set' => [0 => 'int1', 'one' => 'int2'],
            ]
        );

        $this->component->addFacet($facet);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.interval={!key=f1}cat&f.cat.facet.interval.set=int1&f.cat.facet.interval.set={!key=one}int2&facet=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithQueryFacetWithQparser()
    {
        $facet = new FacetQuery(
            [
                'local_key' => 'f1',
                'query' => '{!frange l=100 u=200}price',
            ]
        );

        $this->component->addFacet($facet);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?facet.query={!frange l=100 u=200 key=f1}price&facet=true',
            urldecode($request->getUri())
        );
    }
}

class UnknownFacet extends FacetField
{
    public function getType(): string
    {
        return 'unknown';
    }
}
