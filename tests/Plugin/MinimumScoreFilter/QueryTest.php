<?php
/**
 * Copyright 2014 Bas de Nooijer. All rights reserved.
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

namespace Solarium\Tests\Plugin\MinimumScoreFilter;

use Solarium\Plugin\MinimumScoreFilter\Query;
use Solarium\Tests\QueryType\Select\Query\AbstractQueryTest;

class QueryTest extends AbstractQueryTest
{
    public function setUp()
    {
        $this->query = new Query;
    }

    public function testSetAndGetFilterMode()
    {
        $this->query->setFilterMode(Query::FILTER_MODE_MARK);
        $this->assertEquals(Query::FILTER_MODE_MARK, $this->query->getFilterMode());
    }

    public function testSetAndGetFilterRatio()
    {
        $this->query->setFilterRatio(0.345);
        $this->assertEquals(0.345, $this->query->getFilterRatio());
    }

    public function testClearFields()
    {
        $this->query->addField('newfield');
        $this->query->clearFields();
        $this->assertEquals(array('score'), $this->query->getFields());
    }

    public function testSetAndGetResultClass()
    {
        // Should be ignored
        $this->query->setResultClass('MyResult');
        $this->assertEquals('Solarium\Plugin\MinimumScoreFilter\Result', $this->query->getResultClass());
    }

    public function testAddFields()
    {
        $this->query->clearFields();
        $this->query->addFields(array('field1', 'field2'));
        $this->assertEquals(array('field1', 'field2', 'score'), $this->query->getFields());
    }

    public function testRemoveField()
    {
        $this->query->clearFields();
        $this->query->addFields(array('field1', 'field2'));
        $this->query->removeField('field1');
        $this->assertEquals(array('field2', 'score'), $this->query->getFields());
    }

    public function testSetFields()
    {
        $this->query->clearFields();
        $this->query->addFields(array('field1', 'field2'));
        $this->query->setFields(array('field3', 'field4'));
        $this->assertEquals(array('field3', 'field4', 'score'), $this->query->getFields());
    }

    public function testAddFieldsAsStringWithTrim()
    {
        $this->query->clearFields();
        $this->query->addFields('field1, field2');
        $this->assertEquals(array('field1', 'field2', 'score'), $this->query->getFields());
    }
}