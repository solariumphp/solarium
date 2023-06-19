<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Field;

use Solarium\QueryType\Luke\Result\FlagList;
use Solarium\QueryType\Luke\Result\Schema\Field\CopyFieldDestInterface;
use Solarium\QueryType\Luke\Result\Schema\Field\CopyFieldSourceInterface;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicBasedField;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicField;
use Solarium\QueryType\Luke\Result\Schema\Field\SchemaFieldInterface;
use Solarium\QueryType\Luke\Result\Schema\Type\Type;

class DynamicFieldTest extends AbstractFieldTestCase
{
    /**
     * @var DynamicField
     */
    protected $field;

    public function setUp(): void
    {
        $this->field = new DynamicField('field_*');
    }

    public function testCopyFieldDest()
    {
        $this->assertInstanceOf(CopyFieldDestInterface::class, $this->field);
    }

    public function testCopyFieldSource()
    {
        $this->assertInstanceOf(CopyFieldSourceInterface::class, $this->field);
    }

    public function testSchemaField()
    {
        $this->assertInstanceOf(SchemaFieldInterface::class, $this->field);
    }

    public function testGetName()
    {
        $this->assertSame('field_*', $this->field->getName());
    }

    public function testCreateField()
    {
        $type = new Type('type_a');
        $flags = new FlagList('A-', ['A' => 'A Flag']);

        $this->field->setType($type);
        $this->field->setFlags($flags);
        $this->field->setPositionIncrementGap(1000);

        $newField = $this->field->createField('newField');

        $this->assertInstanceOf(DynamicBasedField::class, $newField);
        $this->assertSame('newField', $newField->getName());
        $this->assertSame($type, $newField->getType());
        $this->assertSame($flags, $newField->getFlags());
        $this->assertSame(1000, $newField->getPositionIncrementGap());
        $this->assertSame($this->field, $newField->getDynamicBase());
    }

    public function testToString()
    {
        $this->assertSame('field_*', (string) $this->field);
    }
}
