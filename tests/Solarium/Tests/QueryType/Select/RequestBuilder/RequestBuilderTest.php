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

namespace Solarium\Tests\QueryType\Select\RequestBuilder;

use Solarium\Core\Client\Request;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Query\FilterQuery;
use Solarium\QueryType\Select\RequestBuilder\RequestBuilder as RequestBuilder;
use Solarium\QueryType\Select\Query\Component\AbstractComponent;

class RequestBuilderTest extends \PHPUnit_Framework_TestCase
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
        $this->query = new Query;
        $this->builder = new RequestBuilder;
    }

    public function testGetMethod()
    {
        $request = $this->builder->build($this->query);
        $this->assertEquals(
            Request::METHOD_GET,
            $request->getMethod()
        );
    }

    public function testSelectUrlWithDefaultValues()
    {
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            null,
            $request->getRawData()
        );

        $this->assertEquals(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score',
            urldecode($request->getUri())
        );
    }

    public function testSelectUrlWithSort()
    {
        $this->query->addSort('id', Query::SORT_ASC);
        $this->query->addSort('name', Query::SORT_DESC);
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            null,
            $request->getRawData()
        );

        $this->assertEquals(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score&sort=id asc,name desc',
            urldecode($request->getUri())
        );
    }

    public function testSelectUrlWithQueryDefaultFieldAndOperator()
    {
        $this->query->setQueryDefaultField('mydefault');
        $this->query->setQueryDefaultOperator(Query::QUERY_OPERATOR_AND);
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            null,
            $request->getRawData()
        );

        $this->assertEquals(
            'select?omitHeader=true&wt=json&json.nl=flat&q=*:*&start=0&rows=10&fl=*,score&q.op=AND&df=mydefault',
            urldecode($request->getUri())
        );
    }

    public function testSelectUrlWithSortAndFilters()
    {
        $this->query->addSort('id', Query::SORT_ASC);
        $this->query->addSort('name', Query::SORT_DESC);
        $this->query->addFilterQuery(new FilterQuery(array('key' => 'f1', 'query' => 'published:true')));
        $this->query->addFilterQuery(
            new FilterQuery(array('key' => 'f2', 'tag' => array('t1', 't2'), 'query' => 'category:23'))
        );
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            null,
            $request->getRawData()
        );

        $this->assertEquals(
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

        $this->assertEquals(
            $request,
            $requestWithNoBuilderComponent
        );
    }

    public function testWithComponent()
    {
        $this->query->getDisMax();
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            'dismax',
            $request->getParam('defType')
        );
    }

    public function testWithEdismaxComponent()
    {
        $this->query->getEdisMax();
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            'edismax',
            $request->getParam('defType')
        );
    }

    public function testWithTags()
    {
        $this->query->setTags(array('t1', 't2'));
        $this->query->setQuery('cat:1');
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            '{!tag=t1,t2}cat:1',
            $request->getParam('q')
        );
    }
}

class TestDummyComponent extends AbstractComponent
{
    public function getType()
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
