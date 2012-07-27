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

namespace Solarium\Tests\QueryType\Update\Query;
use Solarium\QueryType\Update\Query\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase
{

    protected $doc;

    protected $fields = array(
        'id' => 123,
        'name' => 'Test document',
        'categories' => array(1,2,3)
    );

    protected function setUp()
    {
        $this->doc = new Document($this->fields);
    }

    public function testConstructorWithFieldsAndBoosts()
    {
        $fields = array('id' => 1, 'name' => 'testname');
        $boosts = array('name' => 2.7);
        $doc = new Document($fields, $boosts);

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
        $this->doc->addField('myfield', 'myvalue');

        $expectedFields = $this->fields;
        $expectedFields['myfield'] = 'myvalue';

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testAddFieldWithBoost()
    {
        $this->doc->addField('myfield', 'myvalue', 2.3);

        $expectedFields = $this->fields;
        $expectedFields['myfield'] = 'myvalue';

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );

        $this->assertEquals(
            2.3,
            $this->doc->getFieldBoost('myfield')
        );
    }

    public function testAddFieldMultivalue()
    {
        $this->doc->addField('myfield', 'myvalue');

        $expectedFields = $this->fields;
        $expectedFields['myfield'] = 'myvalue';

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );

        $this->doc->addField('myfield', 'mysecondvalue');

        $expectedFields['myfield'] = array('myvalue','mysecondvalue');

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testSetField()
    {
        $this->doc->setField('name', 'newname');

        $expectedFields = $this->fields;
        $expectedFields['name'] = 'newname';

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testSetFieldWithFalsyValue()
    {
        $falsy_value = '';
        $this->doc->setField('name', $falsy_value);

        $expectedFields = $this->fields;
        $expectedFields['name'] = $falsy_value;

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testRemoveField()
    {
        $this->doc->removeField('name');

        $expectedFields = $this->fields;
        unset($expectedFields['name']);

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testRemoveFieldBySettingToNull()
    {
        $this->doc->setField('name', NULL);

        $expectedFields = $this->fields;
        unset($expectedFields['name']);

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testRemoveFieldBoostRemoval()
    {
        $this->doc->setFieldBoost('name',3.2);
        $this->doc->removeField('name');

        $this->assertEquals(
            null,
            $this->doc->getFieldBoost('name')
        );
    }

    public function testRemoveInvalidField()
    {
        $this->doc->removeField('invalidname'); //should silently continue...

        $this->assertEquals(
            $this->fields,
            $this->doc->getFields()
        );
    }

    public function testSetAndGetFieldBoost()
    {
        $this->doc->setFieldBoost('name',2.5);
        $this->assertEquals(
            2.5,
            $this->doc->getFieldBoost('name')
        );
    }

    public function testSetAndGetFieldBoosts()
    {
        $this->doc->setFieldBoost('name',2.5);
        $this->doc->setFieldBoost('category',1.5);
        $this->assertEquals(
           array(
               'name' => 2.5,
               'category' => 1.5,
           ),
            $this->doc->getFieldBoosts()
        );
    }

    public function testGetInvalidFieldBoost()
    {
        $this->assertEquals(
            null,
            $this->doc->getFieldBoost('invalidname')
        );
    }

    public function testSetAndGetBoost()
    {
        $this->doc->setBoost(2.5);
        $this->assertEquals(
            2.5,
            $this->doc->getBoost()
        );
    }

    public function testSetAndGetFieldByProperty()
    {
        $this->doc->name = 'new name';

        $this->assertEquals(
            'new name',
            $this->doc->name
        );
    }

    public function testSetAndGetMultivalueFieldByProperty()
    {
        $values = array('test1', 'test2', 'test3');
        $this->doc->multivaluefield = $values;

        $this->assertEquals(
            $values,
            $this->doc->multivaluefield
        );
    }

    public function testSetAndGetMultivalueFieldByPropertyOverwrite()
    {
        $values = array('test1', 'test2', 'test3');
        $this->doc->name = $values;

        $this->assertEquals(
            $values,
            $this->doc->name
        );
    }

    public function testUnsetFieldByProperty()
    {
        unset($this->doc->name);

        $expectedFields = $this->fields;
        unset($expectedFields['name']);

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testSetFieldAsArray()
    {
        $this->doc['name'] = 'newname';

        $expectedFields = $this->fields;
        $expectedFields['name'] = 'newname';

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testRemoveFieldAsArray()
    {
        unset($this->doc['name']);

        $expectedFields = $this->fields;
        unset($expectedFields['name']);

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testClearFields()
    {
        $this->doc->clear();

        $expectedFields = array();

        $this->assertEquals(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testClearFieldsBoostRemoval()
    {
        $this->doc->setFieldBoost('name', 3.2);
        $this->doc->clear();

        $this->assertEquals(
            null,
            $this->doc->getFieldBoost('name')
        );
    }

}
