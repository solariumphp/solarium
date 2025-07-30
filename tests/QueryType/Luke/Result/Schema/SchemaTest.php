<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicField;
use Solarium\QueryType\Luke\Result\Schema\Field\Field;
use Solarium\QueryType\Luke\Result\Schema\Schema;
use Solarium\QueryType\Luke\Result\Schema\Similarity;
use Solarium\QueryType\Luke\Result\Schema\Type\Type;

class SchemaTest extends TestCase
{
    /**
     * @var Schema
     */
    protected $schema;

    public function setUp(): void
    {
        $this->schema = new Schema();
    }

    public function testSetAndGetFields()
    {
        $fields = [
            'field1' => new Field('field1'),
            'field2' => new Field('field2'),
        ];
        $this->assertSame($this->schema, $this->schema->setFields($fields));
        $this->assertSame($fields, $this->schema->getFields());
    }

    public function testGetField()
    {
        $field = new Field('field3');
        $this->schema->setFields(['field3' => $field]);
        $this->assertSame($field, $this->schema->getField('field3'));
        $this->assertNull($this->schema->getField('field4'));
    }

    public function testSetAndGetDynamicFields()
    {
        $dynamicFields = [
            'dynamicfield1' => new DynamicField('dynamicfield1'),
            'dynamicfield2' => new DynamicField('dynamicfield2'),
        ];
        $this->assertSame($this->schema, $this->schema->setDynamicFields($dynamicFields));
        $this->assertSame($dynamicFields, $this->schema->getDynamicFields());
    }

    public function testGetDynamicField()
    {
        $dynamicField = new DynamicField('dynamicfield3');
        $this->schema->setDynamicFields(['dynamicfield3' => $dynamicField]);
        $this->assertSame($dynamicField, $this->schema->getDynamicField('dynamicfield3'));
        $this->assertNull($this->schema->getDynamicField('dynamicfield4'));
    }

    public function testSetAndGetUniqueKeyField()
    {
        $this->assertNull($this->schema->getUniqueKeyField());

        $uniqueKeyField = new Field('id');
        $this->assertSame($this->schema, $this->schema->setUniqueKeyField($uniqueKeyField));
        $this->assertSame($uniqueKeyField, $this->schema->getUniqueKeyField());
    }

    public function testSetAndGetSimilarity()
    {
        $similarity = new Similarity();
        $this->assertSame($this->schema, $this->schema->setSimilarity($similarity));
        $this->assertSame($similarity, $this->schema->getSimilarity());
    }

    public function testSetAndGetTypes()
    {
        $types = [
            'type_a' => new Type('type_a'),
            'type_b' => new Type('type_b'),
        ];
        $this->assertSame($this->schema, $this->schema->setTypes($types));
        $this->assertSame($types, $this->schema->getTypes());
    }

    public function testGetType()
    {
        $type = new Type('type_c');
        $this->schema->setTypes(['type_c' => $type]);
        $this->assertSame($type, $this->schema->getType('type_c'));
        $this->assertNull($this->schema->getType('type_d'));
    }
}
