<?php

namespace Solarium\Tests\QueryType\Select\Result\Highlighting;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Highlighting\Result;

class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    protected $fields;

    public function setUp(): void
    {
        $this->fields = [
            'field1' => ['content1'],
            'field2' => ['content2'],
        ];

        $this->result = new Result($this->fields);
    }

    public function testGetFields()
    {
        $this->assertSame($this->fields, $this->result->getFields());
    }

    public function testGetField()
    {
        $this->assertSame(
            $this->fields['field2'],
            $this->result->getField('field2')
        );
    }

    public function testGetInvalidField()
    {
        $this->assertSame(
            [],
            $this->result->getField('invalid')
        );
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertSame($this->fields, $items);
    }

    public function testCount()
    {
        $this->assertCount(count($this->fields), $this->result);
    }
}
