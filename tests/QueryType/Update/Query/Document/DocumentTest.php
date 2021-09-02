<?php

namespace Solarium\Tests\QueryType\Update\Query\Document;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Document;

class DocumentTest extends TestCase
{
    /**
     * @var Document
     */
    protected $doc;

    protected $fields = [
        'id' => 123,
        'name' => 'Test document',
        'categories' => [1, 2, 3],
    ];

    public function setUp(): void
    {
        $this->doc = new Document($this->fields);
    }

    public function testConstructorWithFieldsAndBoostsAndModifiers()
    {
        $fields = ['id' => 1, 'name' => 'testname', 'categories' => [4, 5]];
        $boosts = ['name' => 2.7];
        $modifiers = ['name' => Document::MODIFIER_SET];
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

        $this->doc->addField('myfield', 'myvalue2', 2.7);

        $expectedFields['myfield'] = ['myvalue', 'myvalue2'];

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );

        $this->assertSame(
            2.7,
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

        $expectedFields['myfield'] = ['myvalue', 'mysecondvalue'];

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testAddFieldWithModifier()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 1);
        $this->doc->addField('myfield', 'myvalue', null, Document::MODIFIER_SET);

        $this->assertSame(
            ['id' => 1, 'myfield' => 'myvalue'],
            $this->doc->getFields()
        );

        $this->assertSame(
            Document::MODIFIER_SET,
            $this->doc->getFieldModifier('myfield')
        );

        $this->doc->addField('myfield', 'myvalue2', null, Document::MODIFIER_ADD);

        $this->assertSame(
            ['id' => 1, 'myfield' => ['myvalue', 'myvalue2']],
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
            ['id' => 1, 'myfield' => 'myvalue'],
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

    public function testSetFields()
    {
        $this->doc->addField('foo', 'bar');
        $this->doc->setFieldBoost('name', 2.7);
        $this->doc->setFieldModifier('categories', Document::MODIFIER_ADD);

        $fields = ['id' => 1, 'name' => 'testname', 'categories' => [4, 5]];
        $this->doc->setFields($fields);

        $this->assertSame(
            $fields,
            $this->doc->getFields()
        );

        $this->assertNull(
            $this->doc->getFieldBoost('name')
        );

        $this->assertNull(
            $this->doc->getFieldModifier('categories')
        );
    }

    public function testSetFieldsWithBoostsAndModifiers()
    {
        $fields = ['id' => 1, 'name' => 'testname', 'categories' => [4, 5]];
        $boosts = ['name' => 2.7];
        $modifiers = ['categories' => Document::MODIFIER_SET];

        $this->doc->setKey('id');
        $this->doc->setFields($fields, $boosts, $modifiers);

        $this->assertSame(
            $fields,
            $this->doc->getFields()
        );

        $this->assertSame(
            2.7,
            $this->doc->getFieldBoost('name')
        );

        $this->assertSame(
            Document::MODIFIER_SET,
            $this->doc->getFieldModifier('categories')
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

        $this->assertNull(
            $this->doc->getFieldBoost('name')
        );
    }

    public function testRemoveFieldModifierRemoval()
    {
        $this->doc->setFieldModifier('name', Document::MODIFIER_ADD);
        $this->doc->removeField('name');

        $this->assertNull(
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
            [
                'name' => 2.5,
                'category' => 1.5,
            ],
            $this->doc->getFieldBoosts()
        );
    }

    public function testGetInvalidFieldBoost()
    {
        $this->assertNull(
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
        $values = ['test1', 'test2', 'test3'];
        $this->doc->multivaluefield = $values;

        $this->assertSame(
            $values,
            $this->doc->multivaluefield
        );
    }

    public function testSetAndGetMultivalueFieldByPropertyOverwrite()
    {
        $values = ['test1', 'test2', 'test3'];
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

        $expectedFields = [];

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testClearFieldsBoostRemoval()
    {
        $this->doc->setFieldBoost('name', 3.2);
        $this->doc->clear();

        $this->assertNull(
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

        $this->assertNull(
            $this->doc->getFieldModifier('non-existing-field')
        );
    }

    public function testClearFieldsModifierRemoval()
    {
        $this->doc->setFieldModifier('name', Document::MODIFIER_ADD);
        $this->doc->clear();

        $this->assertNull(
            $this->doc->getFieldBoost('name')
        );
    }

    public function testSetFieldModifierWithInvalidValue()
    {
        $this->expectException(RuntimeException::class);
        $this->doc->setFieldModifier('name', 'invalid_modifier_value');
    }

    public function testSetAndGetFieldsUsingModifiers()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 1);
        $this->doc->setField('name', 'newname', null, Document::MODIFIER_SET);

        $this->assertSame(
            ['id' => 1, 'name' => 'newname'],
            $this->doc->getFields()
        );
    }

    public function testSetAndGetFieldsUsingModifiersWithoutKey()
    {
        $this->doc->clear();
        $this->doc->setField('id', 1);
        $this->doc->setField('name', 'newname', null, Document::MODIFIER_SET);

        $this->expectException(RuntimeException::class);
        $this->doc->getFields();
    }

    public function testSetAndGetVersion()
    {
        $this->assertNull(
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

    public function testEscapeByDefaultConstructor()
    {
        $fields = [
            'foo' => 'bar'.chr(15),
            'foos' => ['bar'.chr(15), 'bar'.chr(15).chr(8)],
        ];
        $doc = new Document($fields);

        $this->assertSame('bar ', $doc->foo);
        $this->assertSame(['bar ', 'bar  '], $doc->foos);
    }

    public function testEscapeByDefaultSetByProperty()
    {
        $this->doc->foo = 'bar'.chr(15);
        $this->doc->foos = ['bar'.chr(15), 'bar'.chr(15).chr(8)];

        $this->assertSame('bar ', $this->doc->foo);
        $this->assertSame(['bar ', 'bar  '], $this->doc->foos);
    }

    public function testEscapeByDefaultSetField()
    {
        $this->doc->setField('foo', 'bar'.chr(15));
        $this->doc->setField('foos', ['bar'.chr(15), 'bar'.chr(15).chr(8)]);

        $this->assertSame('bar ', $this->doc->foo);
        $this->assertSame(['bar ', 'bar  '], $this->doc->foos);
    }

    public function testEscapeByDefaultSetFields()
    {
        $fields = [
            'foo' => 'bar'.chr(15),
            'foos' => ['bar'.chr(15), 'bar'.chr(15).chr(8)],
        ];
        $this->doc->setFields($fields);

        $this->assertSame('bar ', $this->doc->foo);
        $this->assertSame(['bar ', 'bar  '], $this->doc->foos);
    }

    public function testEscapeByDefaultAddField()
    {
        $this->doc->setField('foo', 'bar'.chr(15));
        $this->doc->addField('foo', 'bar'.chr(15).chr(8));

        $this->assertSame(['bar ', 'bar  '], $this->doc->foo);
    }

    public function testNoEscapeSetByProperty()
    {
        $this->doc->setFilterControlCharacters(false);
        $this->doc->foo = $value = 'bar'.chr(15);
        $this->doc->foos = $multivalue = ['bar'.chr(15), 'bar'.chr(15).chr(8)];

        $this->assertSame($value, $this->doc->foo);
        $this->assertSame($multivalue, $this->doc->foos);
    }

    public function testNoEscapeSetField()
    {
        $this->doc->setFilterControlCharacters(false);
        $this->doc->setField('foo', $value = 'bar'.chr(15));
        $this->doc->setField('foos', $multivalue = ['bar'.chr(15), 'bar'.chr(15).chr(8)]);

        $this->assertSame($value, $this->doc->foo);
        $this->assertSame($multivalue, $this->doc->foos);
    }

    public function testNoEscapeSetFields()
    {
        $fields = [
            'foo' => $value = 'bar'.chr(15),
            'foos' => $multivalue = ['bar'.chr(15), 'bar'.chr(15).chr(8)],
        ];
        $this->doc->setFilterControlCharacters(false);
        $this->doc->setFields($fields);

        $this->assertSame($value, $this->doc->foo);
        $this->assertSame($multivalue, $this->doc->foos);
    }

    public function testNoEscapeAddField()
    {
        $this->doc->setFilterControlCharacters(false);
        $this->doc->setField('foo', $value1 = 'bar'.chr(15));
        $this->doc->addField('foo', $value2 = 'bar'.chr(15).chr(8));

        $this->assertSame([$value1, $value2], $this->doc->foo);
    }

    public function testSetAndGetFilterControlCharacters()
    {
        $this->doc->setFilterControlCharacters(false);

        $this->assertFalse($this->doc->getFilterControlCharacters());
    }
}
