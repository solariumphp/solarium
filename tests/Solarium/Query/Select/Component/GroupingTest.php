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

class Solarium_Query_Select_Component_GroupingTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_Grouping
     */
    protected $_grouping;

    public function setUp()
    {
        $this->_grouping = new Solarium_Query_Select_Component_Grouping;
    }

    public function testConfigMode()
    {
        $options = array(
            'fields' => array('fieldA','fieldB'),
            'queries' => array('cat:3','cat:4'),
            'limit' => 8,
            'offset' => 1,
            'sort' => 'score desc',
            'mainresult' => false,
            'numberofgroups' => true,
            'cachepercentage' => 45,
            'truncate' => true,
        );

        $this->_grouping->setOptions($options);

        $this->assertEquals($options['fields'], $this->_grouping->getFields());
        $this->assertEquals($options['queries'], $this->_grouping->getQueries());
        $this->assertEquals($options['limit'], $this->_grouping->getLimit());
        $this->assertEquals($options['offset'], $this->_grouping->getOffset());
        $this->assertEquals($options['sort'], $this->_grouping->getSort());
        $this->assertEquals($options['mainresult'], $this->_grouping->getMainResult());
        $this->assertEquals($options['numberofgroups'], $this->_grouping->getNumberOfGroups());
        $this->assertEquals($options['cachepercentage'], $this->_grouping->getCachePercentage());
        $this->assertEquals($options['truncate'], $this->_grouping->getTruncate());
    }

    public function testGetType()
    {
        $this->assertEquals(Solarium_Query_Select::COMPONENT_GROUPING, $this->_grouping->getType());
    }

    public function testSetAndGetFieldsSingle()
    {
        $value = 'fieldC';
        $this->_grouping->setFields($value);

        $this->assertEquals(
            array($value),
            $this->_grouping->getFields()
        );
    }

    public function testSetAndGetFieldsCommaSeparated()
    {
        $value = 'fieldD, fieldE';
        $this->_grouping->setFields($value);

        $this->assertEquals(
            array(
                'fieldD',
                'fieldE',
            ),
            $this->_grouping->getFields()
        );
    }

    public function testSetAndGetFieldsArray()
    {
        $values = array('fieldD', 'fieldE');
        $this->_grouping->setFields($values);

        $this->assertEquals(
            $values,
            $this->_grouping->getFields()
        );
    }

    public function testSetAndGetQueriesSingle()
    {
        $value = 'cat:3';
        $this->_grouping->setQueries($value);

        $this->assertEquals(
            array($value),
            $this->_grouping->getQueries()
        );
    }

    public function testSetAndGetQueriesArray()
    {
        $values = array('cat:5', 'cat:6');
        $this->_grouping->setQueries($values);

        $this->assertEquals(
            $values,
            $this->_grouping->getQueries()
        );
    }

    public function testSetAndGetLimit()
    {
        $value = '12';
        $this->_grouping->setLimit($value);

        $this->assertEquals(
            $value,
            $this->_grouping->getLimit()
        );
    }

    public function testSetAndGetOffset()
    {
        $value = '2';
        $this->_grouping->setOffset($value);

        $this->assertEquals(
            $value,
            $this->_grouping->getOffset()
        );
    }

    public function testSetAndGetSort()
    {
        $value = 'price desc';
        $this->_grouping->setSort($value);

        $this->assertEquals(
            $value,
            $this->_grouping->getSort()
        );
    }

    public function testSetAndGetMainResult()
    {
        $value = true;
        $this->_grouping->setMainResult($value);

        $this->assertEquals(
            $value,
            $this->_grouping->getMainResult()
        );
    }

    public function testSetAndGetNumberOfGroups()
    {
        $value = true;
        $this->_grouping->setNumberOfGroups($value);

        $this->assertEquals(
            $value,
            $this->_grouping->getNumberOfGroups()
        );
    }

    public function testSetAndGetCachePercentage()
    {
        $value = 40;
        $this->_grouping->setCachePercentage($value);

        $this->assertEquals(
            $value,
            $this->_grouping->getCachePercentage()
        );
    }

    public function testSetAndGetTruncate()
    {
        $value = true;
        $this->_grouping->setTruncate($value);

        $this->assertEquals(
            $value,
            $this->_grouping->getTruncate()
        );
    }

}
