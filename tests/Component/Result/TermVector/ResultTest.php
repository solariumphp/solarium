<?php

namespace Solarium\Tests\Component\Result\TermVector;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\TermVector\Document;
use Solarium\Component\Result\TermVector\Result;
use Solarium\Component\Result\TermVector\Warnings;

class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * @var Document[]
     */
    protected $documents;

    /**
     * @var Warnings
     */
    protected $warnings;

    public function setUp(): void
    {
        $this->documents = [
            'key1' => new Document(null, []),
            'key2' => new Document(null, []),
        ];

        $this->warnings = new Warnings(null, null, null, null);

        $this->result = new Result($this->documents, $this->warnings);
    }

    public function testGetDocuments()
    {
        $this->assertSame($this->documents, $this->result->getDocuments());
    }

    public function testGetDocument()
    {
        $this->assertSame($this->documents['key1'], $this->result->getDocument('key1'));
    }

    public function testGetDocumentInvalid()
    {
        $this->assertNull($this->result->getDocument('invalidkey'));
    }

    public function testGetWarnings()
    {
        $this->assertSame($this->warnings, $this->result->getWarnings());
    }

    public function testGetWarningsNull()
    {
        $termVector = new Result([], null);

        $this->assertNull($termVector->getWarnings());
    }

    public function testIterator()
    {
        $documents = [];
        foreach ($this->result as $key => $document) {
            $documents[$key] = $document;
        }

        $this->assertSame($this->documents, $documents);
    }

    public function testCount()
    {
        $this->assertCount(\count($this->documents), $this->result);
    }

    public function testOffsetExists()
    {
        $this->assertTrue($this->result->offsetExists('key1'));
    }

    public function testOffsetExistsUnknown()
    {
        $this->assertFalse($this->result->offsetExists('unknown'));
    }

    public function testOffsetGet()
    {
        $this->assertSame($this->documents['key1'], $this->result->offsetGet('key1'));
    }

    public function testOffsetGetUnknown()
    {
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_WARNING);

        $this->expectExceptionMessage('Undefined array key "unknown"');
        $this->result->offsetGet('unknown');

        restore_error_handler();
    }

    public function testOffsetSetImmutable()
    {
        $this->result->offsetSet('key1', new Document(null, []));
        $this->assertSame($this->documents['key1'], $this->result['key1']);
    }

    public function testOffsetUnsetImmutable()
    {
        $this->result->offsetUnset('key1');
        $this->assertSame($this->documents['key1'], $this->result['key1']);
    }
}
