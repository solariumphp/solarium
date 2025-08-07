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

    public function testGetUniqueKey(): void
    {
        $this->assertSame('key', $this->document->getUniqueKey());
    }

    public function testGetUniqueKeyNull(): void
    {
        $document = new Document(null, $this->fields);

        $this->assertNull($document->getUniqueKey());
    }

    public function testGetFields(): void
    {
        $this->assertSame($this->fields, $this->document->getFields());
    }

    public function testGetFieldsEmpty(): void
    {
        $document = new Document('key', []);

        $this->assertSame([], $document->getFields());
    }

    public function testGetField(): void
    {
        $this->assertSame($this->fields['fieldA'], $this->document->getField('fieldA'));
    }

    public function testGetFieldInvalid(): void
    {
        $this->assertNull($this->document->getField('invalidfield'));
    }

    public function testIterator(): void
    {
        $fields = [];
        foreach ($this->document as $name => $field) {
            $fields[$name] = $field;
        }

        $this->assertSame($this->fields, $fields);
    }

    public function testCount(): void
    {
        $this->assertCount(\count($this->fields), $this->document);
    }

    public function testOffsetExists(): void
    {
        $this->assertTrue($this->document->offsetExists('fieldA'));
        $this->assertFalse($this->document->offsetExists('fieldZ'));
    }

    public function testOffsetGet(): void
    {
        $this->assertSame($this->fields['fieldA'], $this->document->offsetGet('fieldA'));
    }

    public function testOffsetGetUnknown(): void
    {
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_WARNING);

        $this->expectExceptionMessage('Undefined array key "unknown"');
        $this->document->offsetGet('unknown');

        restore_error_handler();
    }

    public function testOffsetSetImmutable(): void
    {
        $this->document->offsetSet('fieldA', new Field('fieldZ', []));
        $this->assertSame($this->fields['fieldA'], $this->document['fieldA']);
    }

    public function testOffsetUnsetImmutable(): void
    {
        $this->document->offsetUnset('fieldA');
        $this->assertSame($this->fields['fieldA'], $this->document['fieldA']);
    }
}
