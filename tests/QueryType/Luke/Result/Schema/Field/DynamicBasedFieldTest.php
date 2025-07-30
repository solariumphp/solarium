<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Field;

use Solarium\QueryType\Luke\Result\Schema\Field\CopyFieldDestInterface;
use Solarium\QueryType\Luke\Result\Schema\Field\CopyFieldSourceInterface;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicBasedField;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicField;
use Solarium\QueryType\Luke\Result\Schema\Field\SchemaFieldInterface;

class DynamicBasedFieldTest extends AbstractFieldTestCase
{
    /**
     * @var DynamicBasedField
     */
    protected $field;

    public function setUp(): void
    {
        $this->field = new DynamicBasedField('fieldA');
    }

    public function testCopyFieldDest()
    {
        $this->assertInstanceOf(CopyFieldDestInterface::class, $this->field);
    }

    public function testCopyFieldSource()
    {
        $this->assertInstanceOf(CopyFieldSourceInterface::class, $this->field);
    }

    public function testNotSchemaField()
    {
        $this->assertNotInstanceOf(SchemaFieldInterface::class, $this->field);
    }

    public function testGetName()
    {
        $this->assertSame('fieldA', $this->field->getName());
    }

    public function testSetAndGetDynamicBase()
    {
        $dynamicBase = new DynamicField('*_a');
        $this->assertSame($this->field, $this->field->setDynamicBase($dynamicBase));
        $this->assertSame($dynamicBase, $this->field->getDynamicBase());
    }

    public function testToString()
    {
        $this->assertSame('fieldA', (string) $this->field);
    }
}
