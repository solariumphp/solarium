<?php

namespace Solarium\Tests\QueryType\Update\Query;

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

    protected $childDocumentFields = [
        'id' => 'parent',
        'name' => 'Parent',
        'cat' => [1, 2],
        'single_child' => [
            'id' => 'single-child',
            'name' => 'Single Child',
            'cat' => [3, 4],
        ],
        'children' => [
            [
                'id' => 'child-1',
                'name' => 'Child 1',
                'cat' => [5, 6],
                // as a single nested document
                'grandchildren' => [
                    'id' => 'grandchild-1-1',
                    'name' => 'Grandchild 1.1',
                    'cat' => [7, 8],
                ],
            ],
            [
                'id' => 'child-2',
                'name' => 'Child 2',
                'cat' => [9, 10],
                // as an array of nested documents
                'grandchildren' => [
                    [
                        'id' => 'grandchild-2-1',
                        'name' => 'Grandchild 2.1',
                        'cat' => [9, 10],
                    ],
                ],
            ],
        ],
        '_childDocuments_' => [
            [
                'id' => 'anonymous-child-1',
                'name' => 'Anonymous Child 1',
                'cat' => [11, 12],
            ],
            [
                'id' => 'anonymous-child-2',
                'name' => 'Anonymous Child 2',
                'cat' => [13, 14],
            ],
        ],
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

    public function testConstructorWithChildDocuments()
    {
        $doc = new Document($this->childDocumentFields);

        $this->assertSame(
            $this->childDocumentFields,
            $doc->getFields()
        );

        $childDocumentFieldsWithSingleAnon = $this->childDocumentFields;
        $childDocumentFieldsWithSingleAnon['_childDocuments_'] = $this->childDocumentFields['_childDocuments_'][0];

        $doc = new Document($childDocumentFieldsWithSingleAnon);

        $this->assertSame(
            $childDocumentFieldsWithSingleAnon,
            $doc->getFields()
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

    public function testAddFieldWithSingleNestedDocument()
    {
        $this->doc->addField('single_child', $this->childDocumentFields['single_child']);

        $expectedFields = $this->fields;
        $expectedFields['single_child'] = [$this->childDocumentFields['single_child']];

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testAddFieldWithNestedDocuments()
    {
        foreach ($this->childDocumentFields['children'] as $child) {
            $this->doc->addField('children', $child);
        }

        $expectedFields = $this->fields;
        $expectedFields['children'] = $this->childDocumentFields['children'];

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testAddFieldWithSingleAnonymousNestedDocument()
    {
        $this->doc->addField('_childDocuments_', $this->childDocumentFields['_childDocuments_'][0]);

        $expectedFields = $this->fields;
        $expectedFields['_childDocuments_'] = [$this->childDocumentFields['_childDocuments_'][0]];

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testAddFieldWithAnonymousNestedDocuments()
    {
        foreach ($this->childDocumentFields['_childDocuments_'] as $child) {
            $this->doc->addField('_childDocuments_', $child);
        }

        $expectedFields = $this->fields;
        $expectedFields['_childDocuments_'] = $this->childDocumentFields['_childDocuments_'];

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
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

    public function testSetFieldWithSingleNestedDocument()
    {
        $this->doc->setField('single_child', $this->childDocumentFields['single_child']);

        $expectedFields = $this->fields;
        $expectedFields['single_child'] = $this->childDocumentFields['single_child'];

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testSetFieldWithNestedDocuments()
    {
        $this->doc->setField('children', $this->childDocumentFields['children']);

        $expectedFields = $this->fields;
        $expectedFields['children'] = $this->childDocumentFields['children'];

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testSetFieldWithSingleAnonymousNestedDocument()
    {
        $this->doc->setField('_childDocuments_', $this->childDocumentFields['_childDocuments_'][0]);

        $expectedFields = $this->fields;
        $expectedFields['_childDocuments_'] = $this->childDocumentFields['_childDocuments_'][0];

        $this->assertSame(
            $expectedFields,
            $this->doc->getFields()
        );
    }

    public function testSetFieldWithAnonymousNestedDocuments()
    {
        $this->doc->setField('_childDocuments_', $this->childDocumentFields['_childDocuments_']);

        $expectedFields = $this->fields;
        $expectedFields['_childDocuments_'] = $this->childDocumentFields['_childDocuments_'];

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

    public function testSetFieldsWithChildDocuments()
    {
        $this->doc->setFields($this->childDocumentFields);

        $this->assertSame(
            $this->childDocumentFields,
            $this->doc->getFields()
        );

        $childDocumentFieldsWithSingleAnon = $this->childDocumentFields;
        $childDocumentFieldsWithSingleAnon['_childDocuments_'] = $this->childDocumentFields['_childDocuments_'][0];

        $this->doc->setFields($childDocumentFieldsWithSingleAnon);

        $this->assertSame(
            $childDocumentFieldsWithSingleAnon,
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
        $this->doc->removeField('invalidname'); // should silently continue...

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

    /**
     * @deprecated No longer supported since Solr 7
     */
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

    public function testSetAndGetFieldWithSingleNestedDocumentByProperty()
    {
        $this->doc->single_child = $this->childDocumentFields['single_child'];

        $this->assertSame(
            $this->childDocumentFields['single_child'],
            $this->doc->single_child
        );
    }

    public function testSetAndGetFieldWithNestedDocumentsByProperty()
    {
        $this->doc->children = $this->childDocumentFields['children'];

        $this->assertSame(
            $this->childDocumentFields['children'],
            $this->doc->children
        );
    }

    public function testSetAndGetFieldWithSingleAnonymousNestedDocumentByProperty()
    {
        $this->doc->_childDocuments_ = $this->childDocumentFields['_childDocuments_'][0];

        $this->assertSame(
            $this->childDocumentFields['_childDocuments_'][0],
            $this->doc->_childDocuments_
        );
    }

    public function testSetAndGetFieldWithAnonymousNestedDocumentsByProperty()
    {
        $this->doc->_childDocuments_ = $this->childDocumentFields['_childDocuments_'];

        $this->assertSame(
            $this->childDocumentFields['_childDocuments_'],
            $this->doc->_childDocuments_
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

    public function testSetAndGetFieldsUsingModifiersWithNonExistingKey()
    {
        $this->doc->clear();
        $this->doc->setKey('id');
        $this->doc->setField('name', 'newname', null, Document::MODIFIER_SET);

        $this->expectException(RuntimeException::class);
        $this->doc->getFields();
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

    public function testJsonSerialize()
    {
        $this->doc->clear();
        $this->doc->setField('single_int', 1);
        $this->doc->setField('multi_int', [2, 3]);
        $this->doc->setField('single_float', 4.1);
        $this->doc->setField('multi_float', [4.2, 4.3]);
        $this->doc->setField('single_string', 'a');
        $this->doc->setField('multi_string', ['b', 'c']);
        $this->doc->setField('single_bool', true);
        $this->doc->setField('multi_bool', [true, false]);
        $this->doc->setField('datetime', new \DateTime('2013-01-15T14:41:58Z'));
        $this->doc->setField('datetimeimmutable', new \DateTimeImmutable('2013-01-15T14:41:58Z'));
        $this->doc->setField('datetimes', [new \DateTime('2013-01-15T14:41:58Z'), new \DateTimeImmutable('2013-01-15T14:41:58Z')]);
        $this->doc->setField('empty_string', '');
        $this->doc->setField('empty_list', []);
        $this->doc->setField('omitted_without_modifier', null);
        $this->doc->setVersion(123);

        $this->assertJsonStringEqualsJsonString(
            '{
                "single_int":1,
                "multi_int":[2,3],
                "single_float":4.1,
                "multi_float":[4.2,4.3],
                "single_string":"a",
                "multi_string":["b","c"],
                "single_bool":true,
                "multi_bool":[true,false],
                "datetime":"2013-01-15T14:41:58Z",
                "datetimeimmutable":"2013-01-15T14:41:58Z",
                "datetimes":["2013-01-15T14:41:58Z","2013-01-15T14:41:58Z"],
                "empty_string":"",
                "empty_list":[],
                "_version_":123
            }',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeWithChildDocuments()
    {
        $this->doc->clear();
        $this->doc->setField('id', 123);
        $this->doc->setField('single', ['id' => 'child-1', 'cat' => [1, 2]]);
        $this->doc->setField('multi', [['id' => 'child-2', 'cat' => [3, 4]], ['id' => 'child-3', 'cat' => [5, 6]]]);

        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"single":{"id":"child-1","cat":[1,2]},"multi":[{"id":"child-2","cat":[3,4]},{"id":"child-3","cat":[5,6]}]}',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeUsingModifiers()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 123);
        $this->doc->setField('set_field', 'newvalue', null, Document::MODIFIER_SET);
        $this->doc->setField('add_field', ['a', 'b'], null, Document::MODIFIER_ADD);
        $this->doc->setField('remove_field', 'c', null, Document::MODIFIER_REMOVE);
        $this->doc->setField('inc_field', 42, null, Document::MODIFIER_INC);

        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"set_field":{"set":"newvalue"},"add_field":{"add":["a","b"]},"remove_field":{"remove":"c"},"inc_field":{"inc":42}}',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeUsingModifierSet()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 123);
        $this->doc->setField('single', 'newvalue', null, Document::MODIFIER_SET);
        $this->doc->setField('multi', [4, 5], null, Document::MODIFIER_SET);
        $this->doc->setField('null', null, null, Document::MODIFIER_SET);
        $this->doc->setField('empty', [], null, Document::MODIFIER_SET);

        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"single":{"set":"newvalue"},"multi":{"set":[4,5]},"null":{"set":null},"empty":{"set":[]}}',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeUsingModifierAdd()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 123);
        $this->doc->setField('single', 4, null, Document::MODIFIER_ADD);
        $this->doc->setField('multi', [5, 6], null, Document::MODIFIER_ADD);

        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"single":{"add":4},"multi":{"add":[5,6]}}',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeUsingModifierAddDistinct()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 123);
        $this->doc->setField('single', 4, null, Document::MODIFIER_ADD_DISTINCT);
        $this->doc->setField('multi', [5, 6], null, Document::MODIFIER_ADD_DISTINCT);

        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"single":{"add-distinct":4},"multi":{"add-distinct":[5,6]}}',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeUsingModifierRemove()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 123);
        $this->doc->setField('single', 4, null, Document::MODIFIER_REMOVE);
        $this->doc->setField('multi', [5, 6], null, Document::MODIFIER_REMOVE);

        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"single":{"remove":4},"multi":{"remove":[5,6]}}',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeUsingModifierRemoveRegex()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 123);
        $this->doc->setField('single', '^single-.+', null, Document::MODIFIER_REMOVEREGEX);
        $this->doc->setField('multi', ['^multi-.+', '.+-multi$'], null, Document::MODIFIER_REMOVEREGEX);

        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"single":{"removeregex":"^single-.+"},"multi":{"removeregex":["^multi-.+",".+-multi$"]}}',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeUsingModifierInc()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 123);
        $this->doc->setField('zero', 0, null, Document::MODIFIER_INC);
        $this->doc->setField('pos_int', 1, null, Document::MODIFIER_INC);
        $this->doc->setField('neg_int', -1, null, Document::MODIFIER_INC);
        $this->doc->setField('pos_float', 3.14, null, Document::MODIFIER_INC);
        $this->doc->setField('neg_float', -2.72, null, Document::MODIFIER_INC);

        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"zero":{"inc":0},"pos_int":{"inc":1},"neg_int":{"inc":-1},"pos_float":{"inc":3.14},"neg_float":{"inc":-2.72}}',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeUsingModifierSetWithChildDocuments()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 123);
        $this->doc->setField('single', ['id' => 'child-1', 'cat' => [1, 2]], null, Document::MODIFIER_SET);
        $this->doc->setField('multi', [['id' => 'child-2', 'cat' => [3, 4]], ['id' => 'child-3', 'cat' => [5, 6]]], null, Document::MODIFIER_SET);

        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"single":{"set":{"id":"child-1","cat":[1,2]}},"multi":{"set":[{"id":"child-2","cat":[3,4]},{"id":"child-3","cat":[5,6]}]}}',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeUsingModifierAddWithChildDocuments()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 123);
        $this->doc->setField('single', ['id' => 'child-4', 'cat' => [7, 8]], null, Document::MODIFIER_ADD);
        $this->doc->setField('multi', [['id' => 'child-5', 'cat' => [9, 10]], ['id' => 'child-6', 'cat' => [11, 12]]], null, Document::MODIFIER_ADD);

        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"single":{"add":{"id":"child-4","cat":[7,8]}},"multi":{"add":[{"id":"child-5","cat":[9,10]},{"id":"child-6","cat":[11,12]}]}}',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeUsingModifierRemoveWithChildDocuments()
    {
        $this->doc->clear();
        $this->doc->setKey('id', 123);
        $this->doc->setField('single', ['id' => 'child-7'], null, Document::MODIFIER_REMOVE);
        $this->doc->setField('multi', [['id' => 'child-8'], ['id' => 'child-9']], null, Document::MODIFIER_REMOVE);

        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"single":{"remove":{"id":"child-7"}},"multi":{"remove":[{"id":"child-8"},{"id":"child-9"}]}}',
            json_encode($this->doc)
        );
    }

    public function testJsonSerializeUsingModifiersWithNonExistingKey()
    {
        $this->doc->clear();
        $this->doc->setKey('id');
        $this->doc->setField('name', 'newname', null, Document::MODIFIER_SET);

        $this->expectException(RuntimeException::class);
        json_encode($this->doc);
    }

    public function testJsonSerializeUsingModifiersWithoutKey()
    {
        $this->doc->clear();
        $this->doc->setField('id', 1);
        $this->doc->setField('name', 'newname', null, Document::MODIFIER_SET);

        $this->expectException(RuntimeException::class);
        json_encode($this->doc);
    }
}
