<?php

namespace Solarium\Tests\QueryType\Select\Result;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\Result;

abstract class AbstractResultTestCase extends TestCase
{
    /**
     * @var SelectDummy
     */
    protected $result;

    protected $numFound;

    protected $maxScore;

    protected $nextCursorMark;

    protected $docs;

    protected $components;

    protected $facetSet;

    protected $moreLikeThis;

    protected $highlighting;

    protected $grouping;

    protected $spellcheck;

    protected $suggester;

    protected $stats;

    protected $debug;

    protected $analytics;

    protected $termVector;

    public function setUp(): void
    {
        $this->numFound = 11;
        $this->maxScore = 0.91;
        $this->nextCursorMark = 'AoEjR0JQ';

        $this->docs = [
            new Document(['id' => 1, 'title' => 'doc1']),
            new Document(['id' => 1, 'title' => 'doc1']),
        ];

        // @todo use dummy classes
        $this->facetSet = null;
        $this->moreLikeThis = null;
        $this->highlighting = null;
        $this->grouping = null;
        $this->spellcheck = null;
        $this->suggester = null;
        $this->stats = null;
        $this->debug = null;
        $this->analytics = null;
        $this->termVector = null;

        $this->components = [
            ComponentAwareQueryInterface::COMPONENT_FACETSET => $this->facetSet,
            ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS => $this->moreLikeThis,
            ComponentAwareQueryInterface::COMPONENT_HIGHLIGHTING => $this->highlighting,
            ComponentAwareQueryInterface::COMPONENT_GROUPING => $this->grouping,
            ComponentAwareQueryInterface::COMPONENT_SPELLCHECK => $this->spellcheck,
            ComponentAwareQueryInterface::COMPONENT_SUGGESTER => $this->suggester,
            ComponentAwareQueryInterface::COMPONENT_STATS => $this->stats,
            ComponentAwareQueryInterface::COMPONENT_DEBUG => $this->debug,
            ComponentAwareQueryInterface::COMPONENT_ANALYTICS => $this->analytics,
            ComponentAwareQueryInterface::COMPONENT_TERMVECTOR => $this->termVector,
        ];

        $this->result = new SelectDummy(1, 12, $this->numFound, $this->maxScore, $this->nextCursorMark, $this->docs, $this->components);
    }

    public function testGetNumFound()
    {
        $this->assertSame($this->numFound, $this->result->getNumFound());
    }

    public function testGetMaxScore()
    {
        $this->assertSame($this->maxScore, $this->result->getMaxScore());
    }

    public function testGetNextCursorMark()
    {
        $this->assertSame($this->nextCursorMark, $this->result->getNextCursorMark());
    }

    public function testGetDocuments()
    {
        $this->assertSame($this->docs, $this->result->getDocuments());
    }

    public function testGetFacetSet()
    {
        $this->assertSame($this->facetSet, $this->result->getFacetSet());
    }

    public function testCount()
    {
        $this->assertSameSize($this->docs, $this->result);
    }

    public function testGetComponents()
    {
        $this->assertSame($this->components, $this->result->getComponents());
    }

    public function testGetComponent()
    {
        $this->assertSame(
            $this->components[ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS],
            $this->result->getComponent(ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS)
        );
    }

    public function testGetInvalidComponent()
    {
        $this->assertNull(
            $this->result->getComponent('invalid')
        );
    }

    public function testGetMoreLikeThis()
    {
        $this->assertSame(
            $this->components[ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS],
            $this->result->getMoreLikeThis()
        );
    }

    public function testGetHighlighting()
    {
        $this->assertSame(
            $this->components[ComponentAwareQueryInterface::COMPONENT_HIGHLIGHTING],
            $this->result->getHighlighting()
        );
    }

    public function testGetGrouping()
    {
        $this->assertSame(
            $this->components[ComponentAwareQueryInterface::COMPONENT_GROUPING],
            $this->result->getGrouping()
        );
    }

    public function testGetSpellcheck()
    {
        $this->assertSame(
            $this->components[ComponentAwareQueryInterface::COMPONENT_SPELLCHECK],
            $this->result->getSpellcheck()
        );
    }

    public function testGetSuggester()
    {
        $this->assertSame(
            $this->components[ComponentAwareQueryInterface::COMPONENT_SUGGESTER],
            $this->result->getSuggester()
        );
    }

    public function testGetStats()
    {
        $this->assertSame(
            $this->components[ComponentAwareQueryInterface::COMPONENT_STATS],
            $this->result->getStats()
        );
    }

    public function testGetDebug()
    {
        $this->assertSame(
            $this->components[ComponentAwareQueryInterface::COMPONENT_DEBUG],
            $this->result->getDebug()
        );
    }

    public function testGetAnalytics()
    {
        $this->assertSame(
            $this->components[ComponentAwareQueryInterface::COMPONENT_ANALYTICS],
            $this->result->getAnalytics()
        );
    }

    public function testGetTermVector()
    {
        $this->assertSame(
            $this->components[ComponentAwareQueryInterface::COMPONENT_TERMVECTOR],
            $this->result->getTermVector()
        );
    }

    public function testIterator()
    {
        $docs = [];
        foreach ($this->result as $key => $doc) {
            $docs[$key] = $doc;
        }

        $this->assertSame($this->docs, $docs);
    }

    public function testGetStatus()
    {
        $this->assertSame(
            1,
            $this->result->getStatus()
        );
    }

    public function testGetQueryTime()
    {
        $this->assertSame(
            12,
            $this->result->getQueryTime()
        );
    }

    public function testNoPartialResults()
    {
        $this->assertNull(
            $this->result->getPartialResults()
        );

        $this->assertFalse(
            $this->result->isPartialResults()
        );
    }

    public function testGetAndIsPartialResults()
    {
        $result = new SelectPartialResultsDummy(0, 5, 0, null, null, [], []);

        $this->assertTrue(
            $result->getPartialResults()
        );

        $this->assertTrue(
            $result->isPartialResults()
        );
    }

    public function testNoSegmentTerminatedEarly()
    {
        $this->assertNull(
            $this->result->getSegmentTerminatedEarly()
        );

        $this->assertFalse(
            $this->result->isSegmentTerminatedEarly()
        );
    }

    public function testGetSegmentTerminatedEarly()
    {
        $result = new SelectSegmentTerminatedEarlyDummy(0, 5, 0, null, null, [], []);

        $this->assertTrue(
            $result->getSegmentTerminatedEarly()
        );

        $this->assertTrue(
            $result->isSegmentTerminatedEarly()
        );
    }
}

class SelectDummy extends Result
{
    protected $parsed = true;

    public function __construct($status, $queryTime, $numfound, $maxscore, $nextcursormark, $docs, $components)
    {
        $this->numfound = $numfound;
        $this->maxscore = $maxscore;
        $this->nextcursormark = $nextcursormark;
        $this->documents = $docs;
        $this->components = $components;
        $this->responseHeader = ['status' => $status, 'QTime' => $queryTime];
    }
}

class SelectPartialResultsDummy extends SelectDummy
{
    public function __construct($status, $queryTime, $numfound, $maxscore, $nextcursormark, $docs, $components)
    {
        parent::__construct($status, $queryTime, $numfound, $maxscore, $nextcursormark, $docs, $components);

        $this->responseHeader['partialResults'] = true;
    }
}

class SelectSegmentTerminatedEarlyDummy extends SelectDummy
{
    public function __construct($status, $queryTime, $numfound, $maxscore, $nextcursormark, $docs, $components)
    {
        parent::__construct($status, $queryTime, $numfound, $maxscore, $nextcursormark, $docs, $components);

        $this->responseHeader['segmentTerminatedEarly'] = true;
    }
}
