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
 *    this listof conditions and the following disclaimer in the documentation
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

namespace Solarium\Tests\Client\RequestBuilder\Select\Component;

class FacetSetTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium\Client\RequestBuilder\Select\Component\FacetSet
     */
    protected $_builder;

    /**
     * @var Solarium\Client\Request
     */
    protected $_request;

    /**
     * @var Solarium\Query\Select\Component\FacetSet();
     */
    protected $_component;


    public function setUp()
    {
        $this->_builder = new \Solarium\Client\RequestBuilder\Select\Component\FacetSet;
        $this->_request = new \Solarium\Client\Request();
        $this->_component = new \Solarium\Query\Select\Component\FacetSet();
    }

    public function testBuildEmptyFacetSet()
    {
        $request = $this->_builder->build($this->_component, $this->_request);
            
        $this->assertEquals(
            array(),
            $request->getParams()
        );

    }

    public function testBuildWithFacets()
    {
        $this->_component->addFacet(new \Solarium\Query\Select\Component\Facet\Field(array('key' => 'f1', 'field' => 'owner')));
        $this->_component->addFacet(new \Solarium\Query\Select\Component\Facet\Query(array('key' => 'f2', 'query' => 'category:23')));
        $this->_component->addFacet(new \Solarium\Query\Select\Component\Facet\MultiQuery(array('key' => 'f3', 'query' => array('f4' => array('query' => 'category:40')))));
        $request = $this->_builder->build($this->_component, $this->_request);

        $this->assertEquals(
            null,
            $request->getRawData()
        );

        $this->assertEquals(
            '?facet=true&facet.field={!key=f1}owner&facet.query={!key=f2}category:23&facet.query={!key=f4}category:40',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithRangeFacet()
    {
        $this->_component->addFacet(new \Solarium\Query\Select\Component\Facet\Range(
            array(
                'key' => 'f1',
                'field' => 'price',
                'start' => '1',
                'end' => 100,
                'gap' => 10,
                'other' => 'all',
                'include' => 'outer'
            )
        ));

        $request = $this->_builder->build($this->_component, $this->_request);

        $this->assertEquals(
            null,
            $request->getRawData()
        );

        $this->assertEquals(
            '?facet=true&facet.range={!key=f1}price&f.price.facet.range.start=1&f.price.facet.range.end=100&f.price.facet.range.gap=10&f.price.facet.range.other=all&f.price.facet.range.include=outer',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithFacetsAndGlobalFacetSettings()
    {
        $this->_component->setMissing(true);
        $this->_component->setLimit(10);
        $this->_component->addFacet(new \Solarium\Query\Select\Component\Facet\Field(array('key' => 'f1', 'field' => 'owner')));
        $this->_component->addFacet(new \Solarium\Query\Select\Component\Facet\Query(array('key' => 'f2', 'query' => 'category:23')));
        $this->_component->addFacet(new \Solarium\Query\Select\Component\Facet\MultiQuery(array('key' => 'f3', 'query' => array('f4' =>array('query' => 'category:40')))));
        $request = $this->_builder->build($this->_component, $this->_request);

        $this->assertEquals(
            null,
            $request->getRawData()
        );

        $this->assertEquals(
            '?facet=true&facet.missing=true&facet.limit=10&facet.field={!key=f1}owner&facet.query={!key=f2}category:23&facet.query={!key=f4}category:40',
            urldecode($request->getUri())
        );
    }

    public function testBuildUnknownFacetType()
    {
        $this->_component->addFacet(new UnknownFacet(array('key' => 'f1', 'field' => 'owner')));
        $this->setExpectedException('Solarium\Exception');
        $request = $this->_builder->build($this->_component, $this->_request);
        $request->getUri();
    }

}

class UnknownFacet extends \Solarium\Query\Select\Component\Facet\Field{

    public function getType()
    {
        return 'unknown';
    }

}