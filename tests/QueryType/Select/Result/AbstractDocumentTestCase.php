<?php

namespace Solarium\Tests\QueryType\Select\Result;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Select\Result\Document;

abstract class AbstractDocumentTestCase extends TestCase
{
    /**
     * @var Document
     */
    protected $doc;

    protected $fields = [
        'id' => 123,
        'name' => 'Test document',
        'categories' => [1, 2, 3],
        'empty_field' => '',
    ];

    public function testGetFields(): void
    {
        $this->assertSame($this->fields, $this->doc->getFields());
    }

    public function testGetFieldAsProperty(): void
    {
        $this->assertSame(
            $this->fields['categories'],
            $this->doc->categories
        );

        $this->assertNull(
            $this->doc->invalidfieldname
        );
    }

    public function testPropertyIsset(): void
    {
        $this->assertTrue(
            isset($this->doc->categories)
        );

        $this->assertFalse(
            isset($this->doc->invalidfieldname)
        );
    }

    public function testPropertyEmpty(): void
    {
        $this->assertEmpty($this->doc->empty_field);
        $this->assertNotEmpty($this->doc->categories);
    }

    public function testSetField(): void
    {
        $this->expectException(RuntimeException::class);
        $this->doc->newField = 'new value';
    }

    public function testIterator(): void
    {
        $fields = [];
        foreach ($this->doc as $key => $field) {
            $fields[$key] = $field;
        }

        $this->assertSame($this->fields, $fields);
    }

    public function testArrayGet(): void
    {
        $this->assertSame(
            $this->fields['categories'],
            $this->doc['categories']
        );

        $this->assertNull(
            $this->doc['invalidfieldname']
        );
    }

    public function testArrayIsset(): void
    {
        $this->assertTrue(
            isset($this->doc['categories'])
        );

        $this->assertFalse(
            isset($this->doc['invalidfieldname'])
        );
    }

    public function testArrayEmpty(): void
    {
        $this->assertEmpty($this->doc['empty_field']);
        $this->assertNotEmpty($this->doc['categories']);
    }

    public function testArraySet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->doc['newField'] = 'new value';
    }

    public function testArrayUnset(): void
    {
        $this->expectException(RuntimeException::class);
        unset($this->doc['newField']);
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->fields, $this->doc);
    }

    public function testJsonSerialize(): void
    {
        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"name":"Test document","categories":[1,2,3],"empty_field":""}',
            json_encode($this->doc)
        );
    }
}
