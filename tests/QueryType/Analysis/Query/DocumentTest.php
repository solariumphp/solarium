<?php

namespace Solarium\Tests\QueryType\Analysis\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Analysis\Query\Document;
use Solarium\QueryType\Select\Result\Document as ReadOnlyDocument;

class DocumentTest extends TestCase
{
    /**
     * @var Document
     */
    protected $query;

    public function setUp()
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
        $doc = new ReadOnlyDocument(array('id' => 1));
        $this->query->addDocument($doc);
        $this->assertSame(
            array($doc),
            $this->query->getDocuments()
        );
    }

    public function testAddAndGetDocuments()
    {
        $doc1 = new ReadOnlyDocument(array('id' => 1));
        $doc2 = new ReadOnlyDocument(array('id' => 2));
        $this->query->addDocuments(array($doc1, $doc2));
        $this->assertSame(
            array($doc1, $doc2),
            $this->query->getDocuments()
        );
    }
}
