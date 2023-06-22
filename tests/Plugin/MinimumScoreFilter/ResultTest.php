<?php

namespace Solarium\Tests\Plugin\MinimumScoreFilter;

use Solarium\Exception\OutOfBoundsException;
use Solarium\Plugin\MinimumScoreFilter\Query;
use Solarium\Plugin\MinimumScoreFilter\Result;
use Solarium\QueryType\Select\Result\Document;
use Solarium\Tests\QueryType\Select\Result\AbstractResultTestCase;

class ResultTest extends AbstractResultTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->maxScore = 0.91;
        $this->docs = [
            new Document(['id' => 1, 'title' => 'doc1', 'score' => 0.91]),
            new Document(['id' => 2, 'title' => 'doc2', 'score' => 0.654]),
            new Document(['id' => 3, 'title' => 'doc3', 'score' => 0.23]),
            new Document(['id' => 4, 'title' => 'doc4', 'score' => 0.08]),
        ];

        $this->result = new FilterResultDummy(1, 12, $this->numFound, $this->maxScore, $this->docs, $this->components, Query::FILTER_MODE_MARK);
    }

    public function testIterator()
    {
        /** @var \Solarium\Plugin\MinimumScoreFilter\Document $doc */
        foreach ($this->result as $key => $doc) {
            $this->assertSame($this->docs[$key]->title, $doc->title);
            $this->assertSame(3 === $key, $doc->markedAsLowScore());
        }
    }

    public function testGetDocuments()
    {
        $this->assertCount(count($this->docs), $this->result->getDocuments());
    }

    public function testIteratorWithRemoveFilter()
    {
        $result = new FilterResultDummy(1, 12, $this->numFound, $this->maxScore, $this->docs, $this->components, Query::FILTER_MODE_REMOVE);
        $docs = [];
        foreach ($result as $key => $doc) {
            $docs[$key] = $doc;
        }

        $this->assertSame($docs[0]->title, $this->docs[0]->title);
        $this->assertSame($docs[1]->title, $this->docs[1]->title);
        $this->assertSame($docs[2]->title, $this->docs[2]->title);
        $this->assertArrayNotHasKey(3, $docs);
    }

    public function testGetDocumentsWithRemoveFilter()
    {
        $result = new FilterResultDummy(1, 12, $this->numFound, $this->maxScore, $this->docs, $this->components, Query::FILTER_MODE_REMOVE);
        $docs = $result->getDocuments();

        $this->assertCount(3, $docs);
        $this->assertSame($docs[0]->title, $this->docs[0]->title);
        $this->assertSame($docs[1]->title, $this->docs[1]->title);
        $this->assertSame($docs[2]->title, $this->docs[2]->title);
    }

    public function testFilterWithInvalidMode()
    {
        $this->expectException(OutOfBoundsException::class);
        $result = new FilterResultDummy(1, 12, $this->numFound, $this->maxScore, $this->docs, $this->components, 'invalid_filter_name');
    }
}

class FilterResultDummy extends Result
{
    protected $parsed = true;

    public function __construct($status, $queryTime, $numfound, $maxscore, $docs, $components, $mode)
    {
        $this->numfound = $numfound;
        $this->maxscore = $maxscore;
        $this->documents = $docs;
        $this->components = $components;
        $this->responseHeader = ['status' => $status, 'QTime' => $queryTime];

        $this->query = new Query();
        $this->query->setFilterRatio(0.2)->setFilterMode($mode);

        $this->mapData(['documents' => $this->documents, 'maxscore' => $this->maxscore]);
    }
}
