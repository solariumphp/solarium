<?php

namespace Solarium\Tests\QueryType\Analysis\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Analysis\Query\Document;
use Solarium\QueryType\Select\Result\Document as ReadOnlyDocument;

class DocumentTest extends TestCase
{
    /**
     * @var Document
     */
    protected $query;

    public function setUp(): void
    {
        $this->query = new Document();
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_ANALYSIS_DOCUMENT, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Analysis\ResponseParser\Document',
            $this->query->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Analysis\RequestBuilder\Document',
            $this->query->getRequestBuilder()
        );
    }

    public function testAddAndGetDocument()
    {
        $doc = new ReadOnlyDocument(['id' => 1]);
        $this->query->addDocument($doc);
        $this->assertSame(
            [$doc],
            $this->query->getDocuments()
        );
    }

    public function testAddAndGetDocuments()
    {
        $doc1 = new ReadOnlyDocument(['id' => 1]);
        $doc2 = new ReadOnlyDocument(['id' => 2]);
        $this->query->addDocuments([$doc1, $doc2]);
        $this->assertSame(
            [$doc1, $doc2],
            $this->query->getDocuments()
        );
    }

    public function testAddInvalidDocument()
    {
        $doc1 = new ReadOnlyDocument(['id' => 1]);
        $doc2 = ['id' => 2];
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Document must implement DocumentInterface.');
        $this->query->addDocuments([$doc1, $doc2]);
    }
}
