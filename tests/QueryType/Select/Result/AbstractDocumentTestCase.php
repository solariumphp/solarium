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

    public function testGetFields()
    {
        $this->assertSame($this->fields, $this->doc->getFields());
    }

    public function testGetFieldAsProperty()
    {
        $this->assertSame(
            $this->fields['categories'],
            $this->doc->categories
        );

        $this->assertNull(
            $this->doc->invalidfieldname
        );
    }

    public function testPropertyIsset()
    {
        $this->assertTrue(
            isset($this->doc->categories)
        );

        $this->assertFalse(
            isset($this->doc->invalidfieldname)
        );
    }

    public function testPropertyEmpty()
    {
        $this->assertEmpty($this->doc->empty_field);
        $this->assertNotEmpty($this->doc->categories);
    }

    public function testSetField()
    {
        $this->expectException(RuntimeException::class);
        $this->doc->newField = 'new value';
    }

    public function testIterator()
    {
        $fields = [];
        foreach ($this->doc as $key => $field) {
            $fields[$key] = $field;
        }

        $this->assertSame($this->fields, $fields);
    }

    public function testArrayGet()
    {
        $this->assertSame(
            $this->fields['categories'],
            $this->doc['categories']
        );

        $this->assertNull(
            $this->doc['invalidfieldname']
        );
    }

    public function testArrayIsset()
    {
        $this->assertTrue(
            isset($this->doc['categories'])
        );

        $this->assertFalse(
            isset($this->doc['invalidfieldname'])
        );
    }

    public function testArrayEmpty()
    {
        $this->assertEmpty($this->doc['empty_field']);
        $this->assertNotEmpty($this->doc['categories']);
    }

    public function testArraySet()
    {
        $this->expectException(RuntimeException::class);
        $this->doc['newField'] = 'new value';
    }

    public function testArrayUnset()
    {
        $this->expectException(RuntimeException::class);
        unset($this->doc['newField']);
    }

    public function testCount()
    {
        $this->assertCount(count($this->fields), $this->doc);
    }

    public function testJsonSerialize()
    {
        $this->assertJsonStringEqualsJsonString(
            '{"id":123,"name":"Test document","categories":[1,2,3],"empty_field":""}',
            json_encode($this->doc)
        );
    }
}
