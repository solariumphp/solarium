<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

namespace Solarium\Tests\QueryType\Select\RequestBuilder\Component;

use Solarium\QueryType\Select\RequestBuilder\Component\FacetSet as RequestBuilder;
use Solarium\QueryType\Select\Query\Component\FacetSet as Component;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Select\Query\Component\Facet\Field as FacetField;
use Solarium\QueryType\Select\Query\Component\Facet\Query as FacetQuery;
use Solarium\QueryType\Select\Query\Component\Facet\MultiQuery as FacetMultiQuery;
use Solarium\QueryType\Select\Query\Component\Facet\Range as FacetRange;
use Solarium\QueryType\Select\Query\Component\Facet\Pivot as FacetPivot;
use Solarium\QueryType\Select\Query\Component\Facet\Interval as FacetInterval;

class FacetSetTest extends \PHPUnit_Framework_TestCase
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
            array(),
            $request->getParams()
        );

    }

    public function testBuildWithFacets()
    {
        $this->component->addFacet(new FacetField(array('key' => 'f1', 'field' => 'owner')));
        $this->component->addFacet(new FacetQuery(array('key' => 'f2', 'query' => 'category:23')));
        $this->component->addFacet(
            new FacetMultiQuery(array('key' => 'f3', 'query' => array('f4' => array('query' => 'category:40'))))
        );

        $request = $this->builder->buildComponent($this->component, $this->request);

        static::assertEquals(
            null,
            $request->getRawData()
        );

        static::assertEquals(
            '?facet=true&facet.field={!key=f1}owner&facet.query={!key=f2}category:23&facet.query={!key=f4}category:40',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithRangeFacet()
    {
        $this->component->addFacet(new FacetRange(
            array(
                'key' => 'f1',
                'field' => 'price',
                'start' => '1',
                'end' => 100,
                'gap' => 10,
                'other' => 'all',
                'include' => 'outer',
                'mincount' => 123
            )
        ));

        $request = $this->builder->buildComponent($this->component, $this->request);

        static::assertEquals(
            null,
            $request->getRawData()
        );

        static::assertEquals(
            '?facet=true&facet.range={!key=f1}price&f.price.facet.range.start=1&f.price.facet.range.end=100&f.price.facet.range.gap=10&f.price.facet.mincount=123&f.price.facet.range.other=all&f.price.facet.range.include=outer',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithRangeFacetExcludingOptionalParams()
    {
        $this->component->addFacet(
            new FacetRange(
                array(
                    'key' => 'f1',
                    'field' => 'price',
                    'start' => '1',
                    'end' => 100,
                    'gap' => 10,
                )
            )
        );

        $request = $this->builder->buildComponent($this->component, $this->request);

        static::assertEquals(
            null,
            $request->getRawData()
        );

        static::assertEquals(
            '?facet=true&facet.range={!key=f1}price&f.price.facet.range.start=1&f.price.facet.range.end=100'.
            '&f.price.facet.range.gap=10',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithFacetsAndGlobalFacetSettings()
    {
        $this->component->setMissing(true);
        $this->component->setLimit(10);
        $this->component->addFacet(new FacetField(array('key' => 'f1', 'field' => 'owner')));
        $this->component->addFacet(new FacetQuery(array('key' => 'f2', 'query' => 'category:23')));
        $this->component->addFacet(
            new FacetMultiQuery(array('key' => 'f3', 'query' => array('f4' =>array('query' => 'category:40'))))
        );

        $request = $this->builder->buildComponent($this->component, $this->request);

        static::assertEquals(
            null,
            $request->getRawData()
        );

        static::assertEquals(
            '?facet=true&facet.missing=true&facet.limit=10&facet.field={!key=f1}owner&facet.query={!key=f2}category:23'.
            '&facet.query={!key=f4}category:40',
            urldecode($request->getUri())
        );
    }

    public function testBuildUnknownFacetType()
    {
        $this->component->addFacet(new UnknownFacet(array('key' => 'f1', 'field' => 'owner')));
        $this->setExpectedException('Solarium\Exception\UnexpectedValueException');
        $request = $this->builder->buildComponent($this->component, $this->request);
        $request->getUri();
    }

    public function testBuildWithPivotFacet()
    {
        $facet = new FacetPivot(
            array(
                'key' => 'f1',
                'fields' => 'cat,inStock',
                'mincount' => 123
            )
        );
        $facet->addExclude('owner');
        $this->component->addFacet($facet);

        $request = $this->builder->buildComponent($this->component, $this->request);

        static::assertEquals(
            null,
            $request->getRawData()
        );

        static::assertEquals(
            '?facet=true&facet.pivot={!key=f1 ex=owner}cat,inStock&facet.pivot.mincount=123',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithPivotStatFacet()
    {
        $facet = new FacetPivot(
            array(
                'key' => 'f1',
                'fields' => 'cat,inStock',
                'stats' => 'piv1'
            )
        );
        $this->component->addFacet($facet);

        $request = $this->builder->buildComponent($this->component, $this->request);

        static::assertEquals(
            null,
            $request->getRawData()
        );

        static::assertEquals(
            '?facet=true&facet.pivot={!stats=piv1}cat,inStock',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithContainsSettings()
    {
        $facet = new FacetField(
            array(
                'key' => 'f1',
                'field' => 'owner',
                'contains' => 'foo',
                'containsignorecase' => true,
            )
        );
        $this->component->addFacet($facet);
        $this->component->setContains('bar');
        $this->component->setContainsIgnoreCase(false);

        $request = $this->builder->buildComponent($this->component, $this->request);

        static::assertEquals(
            null,
            $request->getRawData()
        );

        static::assertEquals(
            '?facet=true&facet.contains=bar&facet.contains.ignoreCase=false&facet.field={!key=f1}owner&f.owner.facet.contains=foo&f.owner.facet.contains.ignoreCase=true',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithIntervalFacet()
    {
        $facet = new FacetInterval(
            array(
                'key' => 'f1',
                'fields' => 'cat,inStock',
                'set' => array(0 => 'int1', 'one' => 'int2'),
            )
        );

        $this->component->addFacet($facet);

        $request = $this->builder->buildComponent($this->component, $this->request);

        static::assertEquals(
            null,
            $request->getRawData()
        );

        static::assertEquals(
            '?facet=true&facet.interval={!key=f1}&f..facet.interval.set=int1&f..facet.interval.set={!key="one"}int2',
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
