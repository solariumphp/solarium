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

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use Solarium\QueryType\Select\Query\Component\Facet\Pivot;
use Solarium\QueryType\Select\Query\Component\FacetSet;

class PivotTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pivot
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new Pivot;
    }

    public function testConfigMode()
    {
        $options = array(
            'fields' => array('abc', 'def'),
            'mincount' => 5,
        );

        $this->facet->setOptions($options);

        $this->assertEquals($options['fields'], $this->facet->getFields());
        $this->assertEquals($options['mincount'], $this->facet->getMinCount());
    }

    public function testGetType()
    {
        $this->assertEquals(
            FacetSet::FACET_PIVOT,
            $this->facet->getType()
        );
    }

    public function testSetMinCount()
    {
        $this->facet->setMinCount(5);

        $this->assertEquals(5, $this->facet->getMinCount());
    }

    public function testAddField()
    {
        $expectedFields = $this->facet->getFields();
        $expectedFields[] = 'newfield';
        $this->facet->addField('newfield');
        $this->assertEquals($expectedFields, $this->facet->getFields());
    }

    public function testClearFields()
    {
        $this->facet->addField('newfield');
        $this->facet->clearFields();
        $this->assertEquals(array(), $this->facet->getFields());
    }

    public function testAddFields()
    {
        $fields = array('field1', 'field2');

        $this->facet->clearFields();
        $this->facet->addFields($fields);
        $this->assertEquals($fields, $this->facet->getFields());
    }

    public function testAddFieldsAsStringWithTrim()
    {
        $this->facet->clearFields();
        $this->facet->addFields('field1, field2');
        $this->assertEquals(array('field1', 'field2'), $this->facet->getFields());
    }

    public function testRemoveField()
    {
        $this->facet->clearFields();
        $this->facet->addFields(array('field1', 'field2'));
        $this->facet->removeField('field1');
        $this->assertEquals(array('field2'), $this->facet->getFields());
    }

    public function testSetFields()
    {
        $this->facet->clearFields();
        $this->facet->addFields(array('field1', 'field2'));
        $this->facet->setFields(array('field3', 'field4'));
        $this->assertEquals(array('field3', 'field4'), $this->facet->getFields());
    }

    public function testAddStat()
    {
        $expectedStats = $this->facet->getStats();
        $expectedStats[] = 'newstat';
        $this->facet->addStat('newstat');
        $this->assertEquals($expectedStats, $this->facet->getStats());
    }

    public function testClearStats()
    {
        $this->facet->addStat('newstat');
        $this->facet->clearStats();
        $this->assertEquals(array(), $this->facet->getStats());
    }

    public function testAddStats()
    {
        $stats = array('stat1', 'stat2');

        $this->facet->clearStats();
        $this->facet->addStats($stats);
        $this->assertEquals($stats, $this->facet->getStats());
    }

    public function testAddStatsAsStringWithTrim()
    {
        $this->facet->clearStats();
        $this->facet->addStats('stat1, stat2');
        $this->assertEquals(array('stat1', 'stat2'), $this->facet->getStats());
    }

    public function testRemoveStat()
    {
        $this->facet->clearStats();
        $this->facet->addStats(array('stat1', 'stat2'));
        $this->facet->removeStat('stat1');
        $this->assertEquals(array('stat2'), $this->facet->getstats());
    }

    public function testSetStats()
    {
        $this->facet->clearStats();
        $this->facet->addStats(array('stat1', 'stat2'));
        $this->facet->setStats(array('stat3', 'stat4'));
        $this->assertEquals(array('stat3', 'stat4'), $this->facet->getStats());
    }
}
