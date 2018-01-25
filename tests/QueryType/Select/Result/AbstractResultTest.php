<?php

namespace Solarium\Tests\QueryType\Select\Result;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\Result;

abstract class AbstractResultTest extends TestCase
{
    /**
     * @var SelectDummy
     */
    protected $result;

    protected $numFound;

    protected $maxScore;

    protected $docs;

    protected $components;

    protected $facetSet;

    protected $moreLikeThis;

    protected $highlighting;

    protected $grouping;

    protected $stats;

    protected $debug;

    protected $spellcheck;

    public function setUp()
    {
        $this->numFound = 11;
        $this->maxScore = 0.91;

        $this->docs = [
            new Document(['id' => 1, 'title' => 'doc1']),
            new Document(['id' => 1, 'title' => 'doc1']),
        ];

        $this->facetSet = 'dummy-facetset-value';
        $this->moreLikeThis = 'dummy-facetset-value';
        $this->highlighting = 'dummy-highlighting-value';
        $this->grouping = 'dummy-grouping-value';
        $this->spellcheck = 'dummy-grouping-value';
        $this->stats = 'dummy-stats-value';
        $this->debug = 'dummy-debug-value';

        $this->components = [
            ComponentAwareQueryInterface::COMPONENT_FACETSET => $this->facetSet,
            ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS => $this->moreLikeThis,
            ComponentAwareQueryInterface::COMPONENT_HIGHLIGHTING => $this->highlighting,
            ComponentAwareQueryInterface::COMPONENT_GROUPING => $this->grouping,
            ComponentAwareQueryInterface::COMPONENT_SPELLCHECK => $this->spellcheck,
            ComponentAwareQueryInterface::COMPONENT_STATS => $this->stats,
            ComponentAwareQueryInterface::COMPONENT_DEBUG => $this->debug,
        ];

        $this->result = new SelectDummy(1, 12, $this->numFound, $this->maxScore, $this->docs, $this->components);
    }

    public function testGetNumFound()
    {
        $this->assertSame($this->numFound, $this->result->getNumFound());
    }

    public function testGetMaxScore()
    {
        $this->assertSame($this->maxScore, $this->result->getMaxScore());
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
        $this->assertSame(count($this->docs), count($this->result));
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
}

class SelectDummy extends Result
{
    protected $parsed = true;

    public function __construct($status, $queryTime, $numfound, $maxscore, $docs, $components)
    {
        $this->numfound = $numfound;
        $this->maxscore = $maxscore;
        $this->documents = $docs;
        $this->components = $components;
        $this->queryTime = $queryTime;
        $this->status = $status;
    }
}
