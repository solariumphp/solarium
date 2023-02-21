<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Field;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Schema\Field\CopyFieldDestInterface;
use Solarium\QueryType\Luke\Result\Schema\Field\CopyFieldSourceInterface;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicBasedField;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicField;
use Solarium\QueryType\Luke\Result\Schema\Field\Field;
use Solarium\QueryType\Luke\Result\Schema\Field\SchemaFieldInterface;
use Solarium\QueryType\Luke\Result\Schema\Field\WildcardField;

class WildcardTest extends TestCase
{
    /**
     * @var WildcardField
     */
    protected $field;

    public function setUp(): void
    {
        $this->field = new WildcardField('field_*');
    }

    public function testNotCopyFieldDest()
    {
        $this->assertNotInstanceOf(CopyFieldDestInterface::class, $this->field);
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
        $this->assertSame('field_*', $this->field->getName());
    }

    public function testAddAndGetCopyDests()
    {
        $copyDests = [
            $field = new Field('field_a'),
            $dynamicField = new DynamicField('*_b'),
            $dynamicBasedField = new DynamicBasedField('field_b'),
        ];
        $this->assertSame($this->field, $this->field->addCopyDest($field));
        $this->assertSame($this->field, $this->field->addCopyDest($dynamicField));
        $this->assertSame($this->field, $this->field->addCopyDest($dynamicBasedField));
        $this->assertSame($copyDests, $this->field->getCopyDests());
    }

    public function testToString()
    {
        $this->assertSame('field_*', (string) $this->field);
    }
}
