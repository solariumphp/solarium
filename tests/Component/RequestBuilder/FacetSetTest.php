<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Field as FacetField;
use Solarium\Component\Facet\Interval as FacetInterval;
use Solarium\Component\Facet\MultiQuery as FacetMultiQuery;
use Solarium\Component\Facet\Pivot as FacetPivot;
use Solarium\Component\Facet\Query as FacetQuery;
use Solarium\Component\Facet\Range as FacetRange;
use Solarium\Component\FacetSet as Component;
use Solarium\Component\RequestBuilder\FacetSet as RequestBuilder;
use Solarium\Core\Client\Request;
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

    public function setUp()
    {
        $this->builder = new RequestBuilder();
        $this->request = new Request();
        $this->component = new Component();
    }

    public function testBuildEmptyFacetSet()
    {
        $request = $this->builder->buildComponent($this->component, $this->request);

        static::assertEquals(
            [],
            $request->getParams()
        );
    }

    public function testBuildWithFacets()
    {
        $this->component->addFacet(new FacetField(['key' => 'f1', 'field' => 'owner']));
        $this->component->addFacet(new FacetQuery(['key' => 'f2', 'query' => 'category:23']));
        $this->component->addFacet(
            new FacetMultiQuery(['key' => 'f3', 'query' => ['f4' => ['query' => 'category:40']]])
        );

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());
        $this->assertEquals(
            '?facet.field={!key=f1}owner&facet.query={!key=f2}category:23&facet.query={!key=f4}category:40&facet=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithRangeFacet()
    {
        $this->component->addFacet(new FacetRange(
            [
                'key' => 'f1',
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

    public function testBuildWithRangeFacetExcludingOptionalParams()
    {
        $this->component->addFacet(
            new FacetRange(
                [
                    'key' => 'f1',
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

    public function testBuildWithFacetsAndGlobalFacetSettings()
    {
        $this->component->setMissing(true);
        $this->component->setLimit(10);
        $this->component->addFacet(new FacetField(['key' => 'f1', 'field' => 'owner']));
        $this->component->addFacet(new FacetQuery(['key' => 'f2', 'query' => 'category:23']));
        $this->component->addFacet(
            new FacetMultiQuery(['key' => 'f3', 'query' => ['f4' => ['query' => 'category:40']]])
        );

        $request = $this->builder->buildComponent($this->component, $this->request);

        static::assertEquals(
            null,
            $request->getRawData()
        );

        static::assertEquals(
            '?facet.field={!key=f1}owner&facet.query={!key=f2}category:23&facet.query={!key=f4}category:40&facet=true&facet.missing=true&facet.limit=10',
            urldecode($request->getUri())
        );
    }

    public function testBuildUnknownFacetType()
    {
        $this->component->addFacet(new UnknownFacet(['key' => 'f1', 'field' => 'owner']));
        $this->expectException(UnexpectedValueException::class);
        $request = $this->builder->buildComponent($this->component, $this->request);
        $request->getUri();
    }

    public function testBuildWithPivotFacet()
    {
        $facet = new FacetPivot(
            [
                'key' => 'f1',
                'fields' => 'cat,inStock',
                'mincount' => 123,
            ]
        );
        $facet->addExclude('owner');
        $this->component->addFacet($facet);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull(
            $request->getRawData()
        );

        $this->assertEquals(
            '?facet.pivot={!key=f1 ex=owner}cat,inStock&facet.pivot.mincount=123&facet=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithPivotStatFacet()
    {
        $facet = new FacetPivot(
            [
                'key' => 'f1',
                'fields' => 'cat,inStock',
                'stats' => 'piv1',
            ]
        );
        $this->component->addFacet($facet);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.pivot={!stats=piv1}cat,inStock&facet=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithContainsSettings()
    {
        $facet = new FacetField(
            [
                'key' => 'f1',
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

    public function testBuildWithIntervalFacet()
    {
        $facet = new FacetInterval(
            [
                'key' => 'f1',
                'fields' => 'cat,inStock',
                'set' => [0 => 'int1', 'one' => 'int2'],
            ]
        );

        $this->component->addFacet($facet);

        $request = $this->builder->buildComponent($this->component, $this->request);

        $this->assertNull($request->getRawData());

        $this->assertEquals(
            '?facet.interval={!key=f1}&f..facet.interval.set=int1&f..facet.interval.set={!key="one"}int2&facet=true',
            urldecode($request->getUri())
        );
    }
}

class UnknownFacet extends FacetField
{
    public function getType()
    {
        return 'unknown';
    }
}
