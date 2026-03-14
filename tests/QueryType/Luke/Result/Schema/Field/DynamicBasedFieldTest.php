<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Field;

use Solarium\QueryType\Luke\Result\Schema\Field\AbstractField;
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
    protected AbstractField $field;

    public function setUp(): void
    {
        $this->field = new DynamicBasedField('fieldA');
    }

    public function testCopyFieldDest(): void
    {
        $this->assertInstanceOf(CopyFieldDestInterface::class, $this->field);
    }

    public function testCopyFieldSource(): void
    {
        $this->assertInstanceOf(CopyFieldSourceInterface::class, $this->field);
    }

    public function testNotSchemaField(): void
    {
        $this->assertNotInstanceOf(SchemaFieldInterface::class, $this->field);
    }

    public function testGetName(): void
    {
        $this->assertSame('fieldA', $this->field->getName());
    }

    public function testSetAndGetDynamicBase(): void
    {
        $dynamicBase = new DynamicField('*_a');
        $this->assertSame($this->field, $this->field->setDynamicBase($dynamicBase));
        $this->assertSame($dynamicBase, $this->field->getDynamicBase());
    }

    public function testToString(): void
    {
        $this->assertSame('fieldA', (string) $this->field);
    }
}
