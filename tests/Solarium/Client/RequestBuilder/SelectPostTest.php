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

class Solarium_Client_RequestBuilder_SelectPostTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select
     */
    protected $_query;

    /**
     * @var Solarium_Client_RequestBuilder_SelectPost
     */
    protected $_builder;

    public function setUp()
    {
        $this->_query = new Solarium_Query_Select;
        $this->_builder = new Solarium_Client_RequestBuilder_SelectPost;
    }

    public function testPostMethod()
    {
        $request = $this->_builder->build($this->_query);
        $this->assertEquals(
            Solarium_Client_Request::METHOD_POST,
            $request->getMethod()
        );
    }

    public function testFormContentTypeHeader()
    {
        $request = $this->_builder->build($this->_query);
        $this->assertEquals(
            array('Content-Type: application/x-www-form-urlencoded'),
            $request->getHeaders()
        );
    }

    public function testSelectUrlWithDefaultValues()
    {
        $request = $this->_builder->build($this->_query);

        $this->assertEquals(
            'q=*:*&start=0&rows=10&fl=*,score&wt=json',
            urldecode($request->getRawData())
        );

        $this->assertEquals(
            'select?',
            $request->getUri()
        );
    }

    public function testSelectUrlWithSort()
    {
        $this->_query->addSort('id', Solarium_Query_Select::SORT_ASC);
        $this->_query->addSort('name', Solarium_Query_Select::SORT_DESC);
        $request = $this->_builder->build($this->_query);

        $this->assertEquals(
            'q=*:*&start=0&rows=10&fl=*,score&wt=json&sort=id asc,name desc',
            urldecode($request->getRawData())
        );

        $this->assertEquals(
            'select?',
            $request->getUri()
        );
    }

    public function testSelectUrlWithSortAndFilters()
    {
        $this->_query->addSort('id', Solarium_Query_Select::SORT_ASC);
        $this->_query->addSort('name', Solarium_Query_Select::SORT_DESC);
        $this->_query->addFilterQuery(new Solarium_Query_Select_FilterQuery(array('key' => 'f1', 'query' => 'published:true')));
        $this->_query->addFilterQuery(new Solarium_Query_Select_FilterQuery(array('key' => 'f2', 'tag' => array('t1','t2'), 'query' => 'category:23')));
        $request = $this->_builder->build($this->_query);

        $this->assertEquals(
            'q=*:*&start=0&rows=10&fl=*,score&wt=json&sort=id asc,name desc&fq=published:true&fq={!tag=t1,t2}category:23',
            urldecode($request->getRawData())
        );

        $this->assertEquals(
            'select?',
            $request->getUri()
        );
    }

    public function testWithComponentNoBuilder()
    {
        $request = $this->_builder->build($this->_query);

        $this->_query->registerComponentType('testcomponent','TestSelectPostDummyComponent');
        $this->_query->getComponent('testcomponent', true);

        $requestWithNoBuilderComponent = $this->_builder->build($this->_query);

        $this->assertEquals(
            $request,
            $requestWithNoBuilderComponent
        );
    }

    public function testWithComponent()
    {
        $this->_query->getDisMax();
        $request = $this->_builder->build($this->_query);

        $rawData = $request->getRawData();
        parse_str($rawData, $params);
        
        $this->assertEquals(
            'dismax',
            $params['defType']
        );
    }
    
}

class TestSelectPostDummyComponent extends Solarium_Query_Select_Component{

    public function getType()
    {
        return 'testcomponent';
    }
}