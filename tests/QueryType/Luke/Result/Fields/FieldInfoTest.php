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

    public function testGetName(): void
    {
        $this->assertSame('fieldA', $this->fieldInfo->getName());
    }

    public function testSetAndGetType(): void
    {
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setType('my_type'));
        $this->assertSame('my_type', $this->fieldInfo->getType());
    }

    public function testSetAndGetSchema(): void
    {
        $schema = new FlagList('-', []);
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setSchema($schema));
        $this->assertSame($schema, $this->fieldInfo->getSchema());
    }

    public function testSetAndGetDynamicBase(): void
    {
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDynamicBase('field_*'));
        $this->assertSame('field_*', $this->fieldInfo->getDynamicBase());

        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDynamicBase(null));
        $this->assertNull($this->fieldInfo->getDynamicBase());
    }

    public function testSetAndGetIndex(): void
    {
        $index = new FlagList('-', []);
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setIndex($index));
        $this->assertSame($index, $this->fieldInfo->getIndex());

        $this->assertSame($this->fieldInfo, $this->fieldInfo->setIndex('(unstored field)'));
        $this->assertSame('(unstored field)', $this->fieldInfo->getIndex());

        $this->assertSame($this->fieldInfo, $this->fieldInfo->setIndex(null));
        $this->assertNull($this->fieldInfo->getIndex());
    }

    public function testSetAndGetDocs(): void
    {
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDocs(42));
        $this->assertSame(42, $this->fieldInfo->getDocs());

        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDocs(null));
        $this->assertNull($this->fieldInfo->getDocs());
    }

    public function testSetAndGetDistinct(): void
    {
        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDistinct(24));
        $this->assertSame(24, $this->fieldInfo->getDistinct());

        $this->assertSame($this->fieldInfo, $this->fieldInfo->setDistinct(null));
        $this->assertNull($this->fieldInfo->getDistinct());
    }

    public function testToString(): void
    {
        $this->assertSame('fieldA', (string) $this->fieldInfo);
    }
}
