<?php

namespace Solarium\Tests\QueryType\Analysis\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Analysis\Query\Document;
use Solarium\QueryType\Analysis\RequestBuilder\Document as DocumentBuilder;
use Solarium\QueryType\Update\Query\Document\Document as InputDocument;

class DocumentTest extends TestCase
{
    /**
     * @var Document
     */
    protected $query;

    /**
     * @var DocumentBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->query = new Document();
        $this->builder = new DocumentBuilder();
    }

    public function testBuild()
    {
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::METHOD_POST, $request->getMethod());
        $this->assertSame($this->builder->getRawData($this->query), $request->getRawData());
    }

    public function testGetRawData()
    {
        // this doc tests data escaping
        $doc1 = new InputDocument(['id' => 1, 'name' => 'doc1', 'cat' => 'my > cat']);

        // this doc tests a multivalue field
        $doc2 = new InputDocument(['id' => 2, 'name' => 'doc2', 'cat' => [1, 2, 3]]);

        $this->query->addDocuments([$doc1, $doc2]);

        $this->assertSame(
            '<docs>'.
            '<doc>'.
            '<field name="id">1</field>'.
            '<field name="name">doc1</field>'.
            '<field name="cat">my &gt; cat</field>'.
            '</doc>'.
            '<doc>'.
            '<field name="id">2</field>'.
            '<field name="name">doc2</field>'.
            '<field name="cat">1</field>'.
            '<field name="cat">2</field>'.
            '<field name="cat">3</field>'.
            '</doc>'.
            '</docs>',
            $this->builder->getRawData($this->query)
        );
    }
}
