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

namespace Solarium\Tests\QueryType\Update\Query\Document;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Update\Query\Document\Document;

class DocumentTest extends TestCase
{
    /**
     * @var Document
     */
    protected $doc;

    protected $fields = array(
        'id' => 123,
        'name' => 'Test document',
        'categories' => array(1, 2, 3)
    );

    protected function setUp()
    {
        $this->doc = new Document($this->fields);
    }

    public function testConstructorWithFieldsAndBoostsAndModifiers()
    {
        $fields = array('id' => 1, 'name' => 'testname');
        $boosts = array('name' => 2.7);
        $modifiers = array('name' => Document::MODIFIER_SET);
        $doc = new Document($fields, $boosts, $modifiers);
        $doc->setKey('id');

        $this->assertSame(
            $fields,
            $doc->getFields()
        );

        $this->assertSame(
            2.7,
            $doc->getFieldBoost('name')
        );

        $this->assertSame(
            Document::MODIFIER_SET,
            $doc->getFieldModifier('name')
        );
    }

    public function testAddFieldNoBoost()
    {
        $this->doc->addField('myfield', 'myvalue');

        $expectedFields = $this->fields;
        $expectedFields['myfield'] = 'myvalue';

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testAddFieldWithBoost()
    {
        $this->doc->addField('myfield', 'myvalue', 2.3);

        $expectedFields = $this->fields;
        $expectedFields['myfield'] = 'myvalue';

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );

        $this->assertSame(
            2.3,
            $this->doc->getFieldBoost('myfield')
        );
    }

    public function testAddFieldMultivalue()
    {
        $this->doc->addField('myfield', 'myvalue');

        $expectedFields = $this->fields;
        $expectedFields['myfield'] = 'myvalue';

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );

        $this->doc->addField('myfield', 'mysecondvalue');

        $expectedFields['myfield'] = array('myvalue', 'mysecondvalue');

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testAddFieldWithModifier()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 1);
        $this->doc->addField('myfield', 'myvalue', null, Document::MODIFIER_ADD);
        $this->doc->addField('myfield', 'myvalue2', null, Document::MODIFIER_ADD);

        $this->assertSame(
            array('id' => 1, 'myfield' => array('myvalue', 'myvalue2')),
            $this->doc->getFields()
        );

        $this->assertSame(
            Document::MODIFIER_ADD,
            $this->doc->getFieldModifier('myfield')
        );
    }

    public function testSetField()
    {
        $this->doc->setField('name', 'newname');

        $expectedFields = $this->fields;
        $expectedFields['name'] = 'newname';

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testSetFieldWithModifier()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 1);
        $this->doc->setField('myfield', 'myvalue', null, Document::MODIFIER_ADD);

        $this->assertSame(
            array('id' => 1, 'myfield' => 'myvalue'),
            $this->doc->getFields()
        );

        $this->assertSame(
            Document::MODIFIER_ADD,
            $this->doc->getFieldModifier('myfield')
        );
    }

    public function testSetFieldWithFalsyValue()
    {
        $falsy_value = '';
        $this->doc->setField('name', $falsy_value);

        $expectedFields = $this->fields;
        $expectedFields['name'] = $falsy_value;

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testRemoveField()
    {
        $this->doc->removeField('name');

        $expectedFields = $this->fields;
        unset($expectedFields['name']);

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testRemoveFieldBySettingNullValueWithModifier()
    {
        $this->doc->setKey('key', 123);
        $this->doc->setField('name', null, null, Document::MODIFIER_SET);

        $expectedFields = $this->fields;
        $expectedFields['key'] = 123;
        $expectedFields['name'] = null;

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testRemoveFieldBySettingToNull()
    {
        $this->doc->setField('name', null);

        $expectedFields = $this->fields;
        unset($expectedFields['name']);

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testRemoveFieldBoostRemoval()
    {
        $this->doc->setFieldBoost('name', 3.2);
        $this->doc->removeField('name');

        $this->assertSame(
            null,
            $this->doc->getFieldBoost('name')
        );
    }

    public function testRemoveInvalidField()
    {
        $this->doc->removeField('invalidname'); //should silently continue...

        $this->assertSame(
            $this->fields,
            $this->doc->getFields()
        );
    }

    public function testSetAndGetFieldBoost()
    {
        $this->doc->setFieldBoost('name', 2.5);
        $this->assertSame(
            2.5,
            $this->doc->getFieldBoost('name')
        );
    }

    public function testSetAndGetFieldBoosts()
    {
        $this->doc->setFieldBoost('name', 2.5);
        $this->doc->setFieldBoost('category', 1.5);
        $this->assertSame(
            array(
                'name' => 2.5,
                'category' => 1.5,
            ),
            $this->doc->getFieldBoosts()
        );
    }

    public function testGetInvalidFieldBoost()
    {
        $this->assertSame(
            null,
            $this->doc->getFieldBoost('invalidname')
        );
    }

    public function testSetAndGetBoost()
    {
        $this->doc->setBoost(2.5);
        $this->assertSame(
            2.5,
            $this->doc->getBoost()
        );
    }

    public function testSetAndGetFieldByProperty()
    {
        $this->doc->name = 'new name';

        $this->assertSame(
            'new name',
            $this->doc->name
        );
    }

    public function testSetAndGetMultivalueFieldByProperty()
    {
        $values = array('test1', 'test2', 'test3');
        $this->doc->multivaluefield = $values;

        $this->assertSame(
            $values,
            $this->doc->multivaluefield
        );
    }

    public function testSetAndGetMultivalueFieldByPropertyOverwrite()
    {
        $values = array('test1', 'test2', 'test3');
        $this->doc->name = $values;

        $this->assertSame(
            $values,
            $this->doc->name
        );
    }

    public function testUnsetFieldByProperty()
    {
        unset($this->doc->name);

        $expectedFields = $this->fields;
        unset($expectedFields['name']);

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testSetFieldAsArray()
    {
        $this->doc['name'] = 'newname';

        $expectedFields = $this->fields;
        $expectedFields['name'] = 'newname';

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testRemoveFieldAsArray()
    {
        unset($this->doc['name']);

        $expectedFields = $this->fields;
        unset($expectedFields['name']);

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testClearFields()
    {
        $this->doc->clear();

        $expectedFields = array();

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testClearFieldsBoostRemoval()
    {
        $this->doc->setFieldBoost('name', 3.2);
        $this->doc->clear();

        $this->assertSame(
            null,
            $this->doc->getFieldBoost('name')
        );
    }

    public function testSetAndGetFieldModifier()
    {
        $this->doc->setFieldModifier('name', Document::MODIFIER_ADD);

        $this->assertSame(
            Document::MODIFIER_ADD,
            $this->doc->getFieldModifier('name')
        );

        $this->assertSame(
            null,
            $this->doc->getFieldModifier('non-existing-field')
        );
    }

    public function testClearFieldsModifierRemoval()
    {
        $this->doc->setFieldModifier('name', Document::MODIFIER_ADD);
        $this->doc->clear();

        $this->assertSame(
            null,
            $this->doc->getFieldBoost('name')
        );
    }

    public function testSetFieldModifierWithInvalidValue()
    {
        $this->expectException('Solarium\Exception\RuntimeException');
        $this->doc->setFieldModifier('name', 'invalid_modifier_value');
    }

    public function testSetAndGetFieldsUsingModifiers()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 1);
        $this->doc->setField('name', 'newname', null, Document::MODIFIER_SET);

        $this->assertSame(
            array('id' => 1, 'name' => 'newname'),
            $this->doc->getFields()
        );
    }

    public function testSetAndGetFieldsUsingModifiersWithoutKey()
    {
        $this->doc->clear();
        $this->doc->setField('id', 1);
        $this->doc->setField('name', 'newname', null, Document::MODIFIER_SET);

        $this->expectException('Solarium\Exception\RuntimeException');
        $this->doc->getFields();
    }

    public function testSetAndGetVersion()
    {
        $this->assertSame(
            null,
            $this->doc->getVersion()
        );

        $this->doc->setVersion(Document::VERSION_MUST_NOT_EXIST);
        $this->assertSame(
            Document::VERSION_MUST_NOT_EXIST,
            $this->doc->getVersion()
        );

        $this->doc->setVersion(234);
        $this->assertSame(
            234,
            $this->doc->getVersion()
        );
    }

    public function testEscapeByDefaultSetField()
    {
        $this->doc->setField('foo', 'bar' . chr(15));

        $this->assertSame('bar ', $this->doc->foo);
    }

    public function testEscapeByDefaultAddField()
    {
        $this->doc->setField('foo', 'bar' . chr(15));
        $this->doc->addField('foo', 'bar' . chr(15) . chr(8));

        $this->assertSame(array('bar ', 'bar  '), $this->doc->foo);
    }

    public function testNoEscapeSetField()
    {
        $this->doc->setFilterControlCharacters(false);
        $this->doc->setField('foo', $value = 'bar' . chr(15));

        $this->assertSame($value, $this->doc->foo);
    }

    public function testNoEscapeAddField()
    {
        $this->doc->setFilterControlCharacters(false);
        $this->doc->setField('foo', $value1 = 'bar' . chr(15));
        $this->doc->addField('foo', $value2 = 'bar' . chr(15) . chr(8));

        $this->assertSame(array($value1, $value2), $this->doc->foo);
    }
}
