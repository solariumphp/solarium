<?php

namespace Solarium\Tests\QueryType\Luke\Result\Fields;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Fields\FieldInfo;
use Solarium\QueryType\Luke\Result\FlagList;

class FieldInfoTest extends TestCase
{
    /**
     * @var FieldInfo
     */
    protected $fieldInfo;

    public function setUp(): void
    {
        $this->fieldInfo = new FieldInfo('fieldA');
    }

    public function testGetName()
    {
        $this->assertSame('fieldA', $this->fieldInfo->getName());
    }

    public function testSetAndGetType()
    {
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setType('my_type'));
        $this->assertSame('my_type', $this->fieldInfo->getType());
    }

    public function testSetAndGetSchema()
    {
        $schema = new FlagList('-', []);
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setSchema($schema));
        $this->assertSame($schema, $this->fieldInfo->getSchema());
    }

    public function testSetAndGetDynamicBase()
    {
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDynamicBase('field_*'));
        $this->assertSame('field_*', $this->fieldInfo->getDynamicBase());

        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDynamicBase(null));
        $this->assertNull($this->fieldInfo->getDynamicBase());
    }

    public function testSetAndGetIndex()
    {
        $index = new FlagList('-', []);
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setIndex($index));
        $this->assertSame($index, $this->fieldInfo->getIndex());

        $this->assertSame($this->fieldInfo, $this->fieldInfo->setIndex('(unstored field)'));
        $this->assertSame('(unstored field)', $this->fieldInfo->getIndex());

        $this->assertSame($this->fieldInfo, $this->fieldInfo->setIndex(null));
        $this->assertNull($this->fieldInfo->getIndex());
    }

    public function testSetAndGetDocs()
    {
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDocs(42));
        $this->assertSame(42, $this->fieldInfo->getDocs());

        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDocs(null));
        $this->assertNull($this->fieldInfo->getDocs());
    }

    public function testSetAndGetDistinct()
    {
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDistinct(24));
        $this->assertSame(24, $this->fieldInfo->getDistinct());

        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDistinct(null));
        $this->assertNull($this->fieldInfo->getDistinct());
    }

    public function testToString()
    {
        $this->assertSame('fieldA', (string) $this->fieldInfo);
    }
}
