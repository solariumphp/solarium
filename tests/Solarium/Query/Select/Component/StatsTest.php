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

class Solarium_Query_Select_Component_StatsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_Stats
     */
    protected $_stats;

    public function setUp()
    {
        $this->_stats = new Solarium_Query_Select_Component_Stats;
    }

    public function testGetType()
    {
        $this->assertEquals(Solarium_Query_Select::COMPONENT_STATS, $this->_stats->getType());
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

        $this->_stats->setOptions($options);

        $this->assertEquals(array('field1','field2'), $this->_stats->getFacets());
        $this->assertEquals(array('f1','f2'), array_keys($this->_stats->getFields()));
    }

    public function testCreateFieldWithKey()
    {
        $field = $this->_stats->createField('mykey');

        // check class
        $this->assertThat($field, $this->isInstanceOf('Solarium_Query_Select_Component_Stats_Field'));

        $this->assertEquals(
            'mykey',
            $field->getKey()
        );
    }

    public function testCreateFieldWithOptions()
    {
        $options = array('key' => 'testkey');
        $field = $this->_stats->createField($options);

        // check class
       $this->assertThat($field, $this->isInstanceOf('Solarium_Query_Select_Component_Stats_Field'));

        // check option forwarding
        $fieldOptions = $field->getOptions();
        $this->assertEquals(
            $options['key'],
            $field->getKey()
        );
    }

    public function testAddAndGetField()
    {
        $field = new Solarium_Query_Select_Component_Stats_Field;
        $field->setKey('f1');
        $this->_stats->addField($field);

        $this->assertEquals(
            $field,
            $this->_stats->getField('f1')
        );
    }

    public function testAddFieldWithOptions()
    {
        $this->_stats->addField(array('key' => 'f1'));

        $this->assertEquals(
            'f1',
            $this->_stats->getField('f1')->getKey()
        );
    }

    public function testAddAndGetFieldWithKey()
    {
        $key = 'f1';

        $fld = $this->_stats->createField($key, true);

        $this->assertEquals(
            $key,
            $fld->getKey()
        );

        $this->assertEquals(
            $fld,
            $this->_stats->getField('f1')
        );
    }

    public function testAddFieldWithoutKey()
    {
        $fld = new Solarium_Query_Select_Component_Stats_Field;

        $this->setExpectedException('Solarium_Exception');
        $this->_stats->addField($fld);
    }

    public function testAddFieldWithUsedKey()
    {
        $f1 = new Solarium_Query_Select_Component_Stats_Field;
        $f1->setKey('f1');

        $f2 = new Solarium_Query_Select_Component_Stats_Field;
        $f2->setKey('f1');

        $this->_stats->addField($f1);
        $this->setExpectedException('Solarium_Exception');
        $this->_stats->addField($f2);
    }

    public function testGetInvalidField()
    {
        $this->assertEquals(
            null,
            $this->_stats->getField('invalidkey')
        );
    }

    public function testAddFields()
    {
        $f1 = new Solarium_Query_Select_Component_Stats_Field;
        $f1->setKey('f1');

        $f2 = new Solarium_Query_Select_Component_Stats_Field;
        $f2->setKey('f2');

        $fields = array('f1' => $f1, 'f2' => $f2);

        $this->_stats->addFields($fields);
        $this->assertEquals(
            $fields,
            $this->_stats->getFields()
        );
    }

    public function testAddFieldsWithOptions()
    {
        $fields = array(
            'f1' => array(''),
            array('key' => 'f2')
        );

        $this->_stats->addFields($fields);
        $fields = $this->_stats->getFields();

        $this->assertEquals( array('f1', 'f2'), array_keys($fields));
    }

    public function testRemoveField()
    {
        $f1 = new Solarium_Query_Select_Component_Stats_Field;
        $f1->setKey('f1');

        $f2 = new Solarium_Query_Select_Component_Stats_Field;
        $f2->setKey('f2');

        $fields = array('f1' => $f1, 'f2' => $f2);

        $this->_stats->addFields($fields);
        $this->_stats->removeField('f1');
        $this->assertEquals(
            array('f2' => $f2),
            $this->_stats->getFields()
        );
    }

    public function testRemoveFieldWithObjectInput()
    {
        $f1 = new Solarium_Query_Select_Component_Stats_Field;
        $f1->setKey('f1');

        $f2 = new Solarium_Query_Select_Component_Stats_Field;
        $f2->setKey('f2');

        $fields = array($f1, $f2);

        $this->_stats->addFields($fields);
        $this->_stats->removeField($f1);
        $this->assertEquals(
            array('f2' => $f2),
            $this->_stats->getFields()
        );
    }

    public function testRemoveInvalidField()
    {
        $f1 = new Solarium_Query_Select_Component_Stats_Field;
        $f1->setKey('f1');

        $f2 = new Solarium_Query_Select_Component_Stats_Field;
        $f2->setKey('f2');

        $fields = array('f1' => $f1, 'f2' => $f2);

        $this->_stats->addFields($fields);
        $this->_stats->removeField('f3'); //continue silently
        $this->assertEquals(
            $fields,
            $this->_stats->getFields()
        );
    }

    public function testClearFields()
    {
        $f1 = new Solarium_Query_Select_Component_Stats_Field;
        $f1->setKey('f1');

        $f2 = new Solarium_Query_Select_Component_Stats_Field;
        $f2->setKey('f2');

        $fields = array($f1, $f2);

        $this->_stats->addFields($fields);
        $this->_stats->clearFields();
        $this->assertEquals(
            array(),
            $this->_stats->getFields()
        );
    }

    public function testSetFields()
    {
        $f1 = new Solarium_Query_Select_Component_Stats_Field;
        $f1->setKey('f1');

        $f2 = new Solarium_Query_Select_Component_Stats_Field;
        $f2->setKey('f2');

        $fields = array($f1, $f2);

        $this->_stats->addFields($fields);

        $f3 = new Solarium_Query_Select_Component_Stats_Field;
        $f3->setKey('f3');

        $f4 = new Solarium_Query_Select_Component_Stats_Field;
        $f4->setKey('f4');

        $fields2 = array('f3' => $f3, 'f4' => $f4);

        $this->_stats->setFields($fields2);

        $this->assertEquals(
            $fields2,
            $this->_stats->getFields()
        );
    }

    public function testAddFacet()
    {
        $expectedFacets = $this->_stats->getFacets();
        $expectedFacets[] = 'newfacet';
        $this->_stats->addFacet('newfacet');
        $this->assertEquals($expectedFacets, $this->_stats->getFacets());
    }

    public function testClearFacets()
    {
        $this->_stats->addFacet('newfacet');
        $this->_stats->clearFacets();
        $this->assertEquals(array(), $this->_stats->getFacets());
    }

    public function testAddFacets()
    {
        $facets = array('facet1','facet2');

        $this->_stats->clearFacets();
        $this->_stats->addFacets($facets);
        $this->assertEquals($facets, $this->_stats->getFacets());
    }

    public function testAddFacetsAsStringWithTrim()
    {
        $this->_stats->clearFacets();
        $this->_stats->addFacets('facet1, facet2');
        $this->assertEquals(array('facet1','facet2'), $this->_stats->getFacets());
    }

    public function testRemoveFacet()
    {
        $this->_stats->clearFacets();
        $this->_stats->addFacets(array('facet1','facet2'));
        $this->_stats->removeFacet('facet1');
        $this->assertEquals(array('facet2'), $this->_stats->getFacets());
    }

    public function testSetFacets()
    {
        $this->_stats->clearFacets();
        $this->_stats->addFacets(array('facet1','facet2'));
        $this->_stats->setFacets(array('facet3','facet4'));
        $this->assertEquals(array('facet3','facet4'), $this->_stats->getFacets());
    }

}
