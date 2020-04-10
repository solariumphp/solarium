<?php

namespace Solarium\Tests\Component\Result\Debug;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Debug\Document;
use Solarium\Component\Result\Debug\DocumentSet;

class DocumentSetTest extends TestCase
{
    /**
     * @var DocumentSet
     */
    protected $result;

    protected $docs;

    public function setUp(): void
    {
        $this->docs = [
            'key1' => new Document('dummy1', true, 0.1, '', []),
            'key2' => new Document('dummy2', false, 0.1, '', []),
        ];
        $this->result = new DocumentSet($this->docs);
    }

    public function testGetDocument()
    {
        $this->assertEquals(
            $this->docs['key1'],
            $this->result->getDocument('key1')
        );
    }

    public function testGetDocumentWithInvalidKey()
    {
        $this->assertNull(
            $this->result->getDocument('invalidkey')
        );
    }

    public function testGetDocuments()
    {
        $this->assertEquals(
            $this->docs,
            $this->result->getDocuments()
        );
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->docs, $items);
    }

    public function testCount()
    {
        $this->assertCount(count($this->docs), $this->result);
    }
}
