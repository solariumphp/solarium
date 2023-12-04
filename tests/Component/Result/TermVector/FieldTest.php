<?php

namespace Solarium\Tests\Component\Result\TermVector;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\TermVector\Field;
use Solarium\Component\Result\TermVector\Term;

class FieldTest extends TestCase
{
    /**
     * @var Field
     */
    protected $field;

    /**
     * @var Term[]
     */
    protected $terms;

    public function setUp(): void
    {
        $this->terms = [
            'term1' => new Term('term1', null, null, null, null, null, null),
            'term2' => new Term('term2', null, null, null, null, null, null),
        ];

        $this->field = new Field('fieldA', $this->terms);
    }

    public function testGetName()
    {
        $this->assertSame('fieldA', $this->field->getName());
    }

    public function testGetTerms()
    {
        $this->assertSame($this->terms, $this->field->getTerms());
    }

    public function testGetTermsEmpty()
    {
        $field = new Field('fieldB', []);

        $this->assertSame([], $field->getTerms());
    }

    public function testGetTerm()
    {
        $this->assertSame($this->terms['term1'], $this->field->getTerm('term1'));
    }

    public function testGetTermInvalid()
    {
        $this->assertNull($this->field->getTerm('invalidterm'));
    }

    public function testIterator()
    {
        $terms = [];
        foreach ($this->field as $key => $term) {
            $terms[$key] = $term;
        }

        $this->assertSame($this->terms, $terms);
    }

    public function testCount()
    {
        $this->assertCount(\count($this->terms), $this->field);
    }

    public function testOffsetExists()
    {
        $this->assertTrue($this->field->offsetExists('term1'));
        $this->assertFalse($this->field->offsetExists('term0'));
    }

    public function testOffsetGet()
    {
        $this->assertSame($this->terms['term1'], $this->field->offsetGet('term1'));
    }

    public function testOffsetGetUnknown()
    {
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_WARNING);

        $this->expectExceptionMessage('Undefined array key "unknown"');
        $this->field->offsetGet('unknown');

        restore_error_handler();
    }

    public function testOffsetSetImmutable()
    {
        $this->field->offsetSet('term1', new Term('term3', null, null, null, null, null, null));
        $this->assertSame($this->terms['term1'], $this->field['term1']);
    }

    public function testOffsetUnsetImmutable()
    {
        $this->field->offsetUnset('term1');
        $this->assertSame($this->terms['term1'], $this->field['term1']);
    }
}
