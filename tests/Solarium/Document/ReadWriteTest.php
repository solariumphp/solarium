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

class Solarium_Document_ReadWriteTest extends PHPUnit_Framework_TestCase
{

    protected $_doc;

    protected $_fields = array(
        'id' => 123,
        'name' => 'Test document',
        'categories' => array(1,2,3)
    );

    protected function setUp()
    {
        $this->_doc = new Solarium_Document_ReadWrite($this->_fields);
    }
    
    public function testConstructorWithFieldsAndBoosts()
    {
        $fields = array('id' => 1, 'name' => 'testname');
        $boosts = array('name' => 2.7);
        $doc = new Solarium_Document_ReadWrite($fields, $boosts);

        $this->assertEquals(
            $fields,
            $doc->getFields()
        );

        $this->assertEquals(
            2.7,
            $doc->getFieldBoost('name')
        );
    }

    public function testAddFieldNoBoost()
    {
        $this->_doc->addField('myfield', 'myvalue');

        $expectedFields = $this->_fields;
        $expectedFields['myfield'] = 'myvalue';

        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );
    }

    public function testAddFieldWithBoost()
    {
        $this->_doc->addField('myfield', 'myvalue', 2.3);

        $expectedFields = $this->_fields;
        $expectedFields['myfield'] = 'myvalue';

        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );

        $this->assertEquals(
            2.3,
            $this->_doc->getFieldBoost('myfield')
        );
    }

    public function testAddFieldMultivalue()
    {
        $this->_doc->addField('myfield', 'myvalue');

        $expectedFields = $this->_fields;
        $expectedFields['myfield'] = 'myvalue';

        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );

        $this->_doc->addField('myfield', 'mysecondvalue');

        $expectedFields['myfield'] = array('myvalue','mysecondvalue');

        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );
    }

    public function testSetField()
    {
        $this->_doc->setField('name', 'newname');

        $expectedFields = $this->_fields;
        $expectedFields['name'] = 'newname';
        
        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );
    }

    public function testSetFieldWithFalsyValue()
    {
        $falsy_value = '';
        $this->_doc->setField('name', $falsy_value);
 
        $expectedFields = $this->_fields;
        $expectedFields['name'] = $falsy_value;
 
        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );
    }

    public function testRemoveField()
    {
        $this->_doc->removeField('name');

        $expectedFields = $this->_fields;
        unset($expectedFields['name']);

        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );
    }

    public function testRemoveFieldBySettingToNull()
    {
        $this->_doc->setField('name', NULL);

        $expectedFields = $this->_fields;
        unset($expectedFields['name']);

        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );
    }

    public function testRemoveFieldBoostRemoval()
    {
        $this->_doc->setFieldBoost('name',3.2);
        $this->_doc->removeField('name');

        $this->assertEquals(
            null,
            $this->_doc->getFieldBoost('name')
        );
    }


    public function testRemoveInvalidField()
    {
        $this->_doc->removeField('invalidname'); //should silently continue...

        $this->assertEquals(
            $this->_fields,
            $this->_doc->getFields()
        );
    }

    public function testSetAndGetFieldBoost()
    {
        $this->_doc->setFieldBoost('name',2.5);
        $this->assertEquals(
            2.5,
            $this->_doc->getFieldBoost('name')
        );
    }

    public function testGetInvalidFieldBoost()
    {
        $this->assertEquals(
            null,
            $this->_doc->getFieldBoost('invalidname')
        );
    }

    public function testSetAndGetBoost()
    {
        $this->_doc->setBoost(2.5);
        $this->assertEquals(
            2.5,
            $this->_doc->getBoost()
        );
    }

    public function testSetAndGetFieldByProperty()
    {
        $this->_doc->name = 'new name';

        $this->assertEquals(
            'new name',
            $this->_doc->name
        );
    }

    public function testSetAndGetMultivalueFieldByProperty()
    {
        $values = array('test1', 'test2', 'test3');
        $this->_doc->multivaluefield = $values;

        $this->assertEquals(
            $values,
            $this->_doc->multivaluefield
        );
    }

    public function testSetAndGetMultivalueFieldByPropertyOverwrite()
    {
        $values = array('test1', 'test2', 'test3');
        $this->_doc->name = $values;

        $this->assertEquals(
            $values,
            $this->_doc->name
        );
    }

    public function testUnsetFieldByProperty()
    {
        unset($this->_doc->name);

        $expectedFields = $this->_fields;
        unset($expectedFields['name']);

        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );
    }

    public function testSetFieldAsArray()
    {
        $this->_doc['name'] = 'newname';

        $expectedFields = $this->_fields;
        $expectedFields['name'] = 'newname';

        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );
    }

    public function testRemoveFieldAsArray()
    {
        unset($this->_doc['name']);

        $expectedFields = $this->_fields;
        unset($expectedFields['name']);

        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );
    }

    public function testClearFields()
    {
        $this->_doc->clear();

        $expectedFields = array();

        $this->assertEquals(
            $expectedFields,
            $this->_doc->getFields()
        );
    }

    public function testClearFieldsBoostRemoval()
    {
        $this->_doc->setFieldBoost('name', 3.2);
        $this->_doc->clear();

        $expectedFields = array();

        $this->assertEquals(
            null,
            $this->_doc->getFieldBoost('name')
        );
    }
    
}
