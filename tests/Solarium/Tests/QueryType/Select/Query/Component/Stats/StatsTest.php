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

namespace Solarium\Tests\QueryType\Select\Query\Component\Stats;

use Solarium\QueryType\Select\Query\Component\Stats\Stats;
use Solarium\QueryType\Select\Query\Component\Stats\Field;
use Solarium\QueryType\Select\Query\Query;

class StatsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Stats
     */
    protected $stats;

    public function setUp()
    {
        $this->stats = new Stats;
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMPONENT_STATS, $this->stats->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\ResponseParser\Component\Stats',
            $this->stats->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\RequestBuilder\Component\Stats',
            $this->stats->getRequestBuilder()
        );
    }

    public function testConfigMode()
    {
        $options = array(
            'facet' => 'field1, field2',
            'field' => array(
                'f1' => array(),
                'f2' => array(),
            )
        );

        $this->stats->setOptions($options);

        $this->assertEquals(array('field1', 'field2'), $this->stats->getFacets());
        $this->assertEquals(array('f1', 'f2'), array_keys($this->stats->getFields()));
    }

    public function testCreateFieldWithKey()
    {
        $field = $this->stats->createField('mykey');

        // check class
        $this->assertThat($field, $this->isInstanceOf('Solarium\QueryType\Select\Query\Component\Stats\Field'));

        $this->assertEquals(
            'mykey',
            $field->getKey()
        );
    }

    public function testCreateFieldWithOptions()
    {
        $options = array('key' => 'testkey');
        $field = $this->stats->createField($options);

        // check class
        $this->assertThat($field, $this->isInstanceOf('Solarium\QueryType\Select\Query\Component\Stats\Field'));

        // check option forwarding
        $fieldOptions = $field->getOptions();
        $this->assertEquals(
            $options['key'],
            $field->getKey()
        );
    }

    public function testAddAndGetField()
    {
        $field = new Field;
        $field->setKey('f1');
        $this->stats->addField($field);

        $this->assertEquals(
            $field,
            $this->stats->getField('f1')
        );
    }

    public function testAddFieldWithOptions()
    {
        $this->stats->addField(array('key' => 'f1'));

        $this->assertEquals(
            'f1',
            $this->stats->getField('f1')->getKey()
        );
    }

    public function testAddAndGetFieldWithKey()
    {
        $key = 'f1';

        $fld = $this->stats->createField($key, true);

        $this->assertEquals(
            $key,
            $fld->getKey()
        );

        $this->assertEquals(
            $fld,
            $this->stats->getField('f1')
        );
    }

    public function testAddFieldWithoutKey()
    {
        $fld = new Field;

        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->stats->addField($fld);
    }

    public function testAddFieldWithUsedKey()
    {
        $f1 = new Field;
        $f1->setKey('f1');

        $f2 = new Field;
        $f2->setKey('f1');

        $this->stats->addField($f1);
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->stats->addField($f2);
    }

    public function testGetInvalidField()
    {
        $this->assertEquals(
            null,
            $this->stats->getField('invalidkey')
        );
    }

    public function testAddFields()
    {
        $f1 = new Field;
        $f1->setKey('f1');

        $f2 = new Field;
        $f2->setKey('f2');

        $fields = array('f1' => $f1, 'f2' => $f2);

        $this->stats->addFields($fields);
        $this->assertEquals(
            $fields,
            $this->stats->getFields()
        );
    }

    public function testAddFieldsWithOptions()
    {
        $fields = array(
            'f1' => array(''),
            array('key' => 'f2')
        );

        $this->stats->addFields($fields);
        $fields = $this->stats->getFields();

        $this->assertEquals(array('f1', 'f2'), array_keys($fields));
    }

    public function testRemoveField()
    {
        $f1 = new Field;
        $f1->setKey('f1');

        $f2 = new Field;
        $f2->setKey('f2');

        $fields = array('f1' => $f1, 'f2' => $f2);

        $this->stats->addFields($fields);
        $this->stats->removeField('f1');
        $this->assertEquals(
            array('f2' => $f2),
            $this->stats->getFields()
        );
    }

    public function testRemoveFieldWithObjectInput()
    {
        $f1 = new Field;
        $f1->setKey('f1');

        $f2 = new Field;
        $f2->setKey('f2');

        $fields = array($f1, $f2);

        $this->stats->addFields($fields);
        $this->stats->removeField($f1);
        $this->assertEquals(
            array('f2' => $f2),
            $this->stats->getFields()
        );
    }

    public function testRemoveInvalidField()
    {
        $f1 = new Field;
        $f1->setKey('f1');

        $f2 = new Field;
        $f2->setKey('f2');

        $fields = array('f1' => $f1, 'f2' => $f2);

        $this->stats->addFields($fields);
        $this->stats->removeField('f3'); //continue silently
        $this->assertEquals(
            $fields,
            $this->stats->getFields()
        );
    }

    public function testClearFields()
    {
        $f1 = new Field;
        $f1->setKey('f1');

        $f2 = new Field;
        $f2->setKey('f2');

        $fields = array($f1, $f2);

        $this->stats->addFields($fields);
        $this->stats->clearFields();
        $this->assertEquals(
            array(),
            $this->stats->getFields()
        );
    }

    public function testSetFields()
    {
        $f1 = new Field;
        $f1->setKey('f1');

        $f2 = new Field;
        $f2->setKey('f2');

        $fields = array($f1, $f2);

        $this->stats->addFields($fields);

        $f3 = new Field;
        $f3->setKey('f3');

        $f4 = new Field;
        $f4->setKey('f4');

        $fields2 = array('f3' => $f3, 'f4' => $f4);

        $this->stats->setFields($fields2);

        $this->assertEquals(
            $fields2,
            $this->stats->getFields()
        );
    }

    public function testAddFacet()
    {
        $expectedFacets = $this->stats->getFacets();
        $expectedFacets[] = 'newfacet';
        $this->stats->addFacet('newfacet');
        $this->assertEquals($expectedFacets, $this->stats->getFacets());
    }

    public function testClearFacets()
    {
        $this->stats->addFacet('newfacet');
        $this->stats->clearFacets();
        $this->assertEquals(array(), $this->stats->getFacets());
    }

    public function testAddFacets()
    {
        $facets = array('facet1', 'facet2');

        $this->stats->clearFacets();
        $this->stats->addFacets($facets);
        $this->assertEquals($facets, $this->stats->getFacets());
    }

    public function testAddFacetsAsStringWithTrim()
    {
        $this->stats->clearFacets();
        $this->stats->addFacets('facet1, facet2');
        $this->assertEquals(array('facet1', 'facet2'), $this->stats->getFacets());
    }

    public function testRemoveFacet()
    {
        $this->stats->clearFacets();
        $this->stats->addFacets(array('facet1', 'facet2'));
        $this->stats->removeFacet('facet1');
        $this->assertEquals(array('facet2'), $this->stats->getFacets());
    }

    public function testSetFacets()
    {
        $this->stats->clearFacets();
        $this->stats->addFacets(array('facet1', 'facet2'));
        $this->stats->setFacets(array('facet3', 'facet4'));
        $this->assertEquals(array('facet3', 'facet4'), $this->stats->getFacets());
    }
}
