<?php

namespace Solarium\Tests\Component\Result\TermVector;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\TermVector\Document;
use Solarium\Component\Result\TermVector\Field;

class DocumentTest extends TestCase
{
    /**
     * @var Document
     */
    protected $document;

    /**
     * @var Field[]
     */
    protected $fields;

    public function setUp(): void
    {
        $this->fields = [
            'fieldA' => new Field('fieldA', []),
            'fieldB' => new Field('fieldB', []),
        ];

        $this->document = new Document('key', $this->fields);
    }

    public function testGetUniqueKey()
    {
        $this->assertSame('key', $this->document->getUniqueKey());
    }

    public function testGetUniqueKeyNull()
    {
        $document = new Document(null, $this->fields);

        $this->assertNull($document->getUniqueKey());
    }

    public function testGetFields()
    {
        $this->assertSame($this->fields, $this->document->getFields());
    }

    public function testGetFieldsEmpty()
    {
        $document = new Document('key', []);

        $this->assertSame([], $document->getFields());
    }

    public function testGetField()
    {
        $this->assertSame($this->fields['fieldA'], $this->document->getField('fieldA'));
    }

    public function testGetFieldInvalid()
    {
        $this->assertNull($this->document->getField('invalidfield'));
    }

    public function testIterator()
    {
        $fields = [];
        foreach ($this->document as $name => $field) {
            $fields[$name] = $field;
        }

        $this->assertSame($this->fields, $fields);
    }

    public function testCount()
    {
        $this->assertCount(\count($this->fields), $this->document);
    }

    public function testOffsetExists()
    {
        $this->assertTrue($this->document->offsetExists('fieldA'));
        $this->assertFalse($this->document->offsetExists('fieldZ'));
    }

    public function testOffsetGet()
    {
        $this->assertSame($this->fields['fieldA'], $this->document->offsetGet('fieldA'));
    }

    public function testOffsetGetUnknown()
    {
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_WARNING);

        $this->expectExceptionMessage('Undefined array key "unknown"');
        $this->document->offsetGet('unknown');

        restore_error_handler();
    }

    public function testOffsetSetImmutable()
    {
        $this->document->offsetSet('fieldA', new Field('fieldZ', []));
        $this->assertSame($this->fields['fieldA'], $this->document['fieldA']);
    }

    public function testOffsetUnsetImmutable()
    {
        $this->document->offsetUnset('fieldA');
        $this->assertSame($this->fields['fieldA'], $this->document['fieldA']);
    }
}
