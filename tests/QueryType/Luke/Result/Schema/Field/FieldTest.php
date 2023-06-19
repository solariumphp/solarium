<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Field;

use Solarium\QueryType\Luke\Result\Schema\Field\CopyFieldDestInterface;
use Solarium\QueryType\Luke\Result\Schema\Field\CopyFieldSourceInterface;
use Solarium\QueryType\Luke\Result\Schema\Field\Field;
use Solarium\QueryType\Luke\Result\Schema\Field\SchemaFieldInterface;

class FieldTest extends AbstractFieldTestCase
{
    /**
     * @var Field
     */
    protected $field;

    public function setUp(): void
    {
        $this->field = new Field('fieldA');
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
        $this->assertSame('fieldA', $this->field->getName());
    }

    public function testToString()
    {
        $this->assertSame('fieldA', (string) $this->field);
    }
}
