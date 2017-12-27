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

namespace Solarium\Tests\QueryType\Select\Query\Component;

use Solarium\QueryType\Select\Query\Component\Grouping;
use Solarium\QueryType\Select\Query\Query;

class GroupingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Grouping
     */
    protected $grouping;

    public function setUp()
    {
        $this->grouping = new Grouping;
    }

    public function testConfigMode()
    {
        $options = array(
            'fields' => array('fieldA', 'fieldB'),
            'queries' => array('cat:3', 'cat:4'),
            'limit' => 8,
            'offset' => 1,
            'sort' => 'score desc',
            'mainresult' => false,
            'numberofgroups' => true,
            'cachepercentage' => 45,
            'truncate' => true,
            'function' => 'log(foo)',
            'format' => 'grouped',
            'facet' => 'true',
        );

        $this->grouping->setOptions($options);

        $this->assertEquals($options['fields'], $this->grouping->getFields());
        $this->assertEquals($options['queries'], $this->grouping->getQueries());
        $this->assertEquals($options['limit'], $this->grouping->getLimit());
        $this->assertEquals($options['offset'], $this->grouping->getOffset());
        $this->assertEquals($options['sort'], $this->grouping->getSort());
        $this->assertEquals($options['mainresult'], $this->grouping->getMainResult());
        $this->assertEquals($options['numberofgroups'], $this->grouping->getNumberOfGroups());
        $this->assertEquals($options['cachepercentage'], $this->grouping->getCachePercentage());
        $this->assertEquals($options['truncate'], $this->grouping->getTruncate());
        $this->assertEquals($options['function'], $this->grouping->getFunction());
        $this->assertEquals($options['format'], $this->grouping->getFormat());
        $this->assertEquals($options['facet'], $this->grouping->getFacet());
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMPONENT_GROUPING, $this->grouping->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\ResponseParser\Component\Grouping',
            $this->grouping->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\RequestBuilder\Component\Grouping',
            $this->grouping->getRequestBuilder()
        );
    }

    public function testSetAndGetResultQueryGroupClass()
    {
        $value = 'classX';
        $this->grouping->setResultQueryGroupClass($value);

        $this->assertEquals(
            $value,
            $this->grouping->getResultQueryGroupClass()
        );
    }

    public function testSetAndGetResultValueGroupClass()
    {
        $value = 'classY';
        $this->grouping->setResultValueGroupClass($value);

        $this->assertEquals(
            $value,
            $this->grouping->getResultValueGroupClass()
        );
    }

    public function testSetAndGetFieldsSingle()
    {
        $value = 'fieldC';
        $this->grouping->setFields($value);

        $this->assertEquals(
            array($value),
            $this->grouping->getFields()
        );
    }

    public function testSetAndGetFieldsCommaSeparated()
    {
        $value = 'fieldD, fieldE';
        $this->grouping->setFields($value);

        $this->assertEquals(
            array(
                'fieldD',
                'fieldE',
            ),
            $this->grouping->getFields()
        );
    }

    public function testSetAndGetFieldsArray()
    {
        $values = array('fieldD', 'fieldE');
        $this->grouping->setFields($values);

        $this->assertEquals(
            $values,
            $this->grouping->getFields()
        );
    }

    public function testSetAndGetQueriesSingle()
    {
        $value = 'cat:3';
        $this->grouping->setQueries($value);

        $this->assertEquals(
            array($value),
            $this->grouping->getQueries()
        );
    }

    public function testSetAndGetQueriesArray()
    {
        $values = array('cat:5', 'cat:6');
        $this->grouping->setQueries($values);

        $this->assertEquals(
            $values,
            $this->grouping->getQueries()
        );
    }

    public function testSetAndGetLimit()
    {
        $value = '12';
        $this->grouping->setLimit($value);

        $this->assertEquals(
            $value,
            $this->grouping->getLimit()
        );
    }

    public function testSetAndGetOffset()
    {
        $value = '2';
        $this->grouping->setOffset($value);

        $this->assertEquals(
            $value,
            $this->grouping->getOffset()
        );
    }

    public function testSetAndGetSort()
    {
        $value = 'price desc';
        $this->grouping->setSort($value);

        $this->assertEquals(
            $value,
            $this->grouping->getSort()
        );
    }

    public function testSetAndGetMainResult()
    {
        $value = true;
        $this->grouping->setMainResult($value);

        $this->assertEquals(
            $value,
            $this->grouping->getMainResult()
        );
    }

    public function testSetAndGetNumberOfGroups()
    {
        $value = true;
        $this->grouping->setNumberOfGroups($value);

        $this->assertEquals(
            $value,
            $this->grouping->getNumberOfGroups()
        );
    }

    public function testSetAndGetCachePercentage()
    {
        $value = 40;
        $this->grouping->setCachePercentage($value);

        $this->assertEquals(
            $value,
            $this->grouping->getCachePercentage()
        );
    }

    public function testSetAndGetTruncate()
    {
        $value = true;
        $this->grouping->setTruncate($value);

        $this->assertEquals(
            $value,
            $this->grouping->getTruncate()
        );
    }

    public function testSetAndGetFunction()
    {
        $value = 'log(foo)';
        $this->grouping->setFunction($value);

        $this->assertEquals(
            $value,
            $this->grouping->getFunction()
        );
    }

    public function testSetAndGetFormat()
    {
        $value = 'grouped';
        $this->grouping->setFormat($value);

        $this->assertEquals(
            $value,
            $this->grouping->getFormat()
        );
    }

    public function testSetAndGetFacet()
    {
        $value = true;
        $this->grouping->setFacet($value);

        $this->assertEquals(
            $value,
            $this->grouping->getFacet()
        );
    }
}
