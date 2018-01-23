<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

namespace Solarium\Tests\QueryType\Select\Result;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\QueryType\Select\Query\Query;
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

        $this->docs = array(
            new Document(array('id'=>1, 'title'=>'doc1')),
            new Document(array('id'=>1, 'title'=>'doc1')),
        );

        $this->facetSet = 'dummy-facetset-value';
        $this->moreLikeThis = 'dummy-facetset-value';
        $this->highlighting = 'dummy-highlighting-value';
        $this->grouping = 'dummy-grouping-value';
        $this->spellcheck = 'dummy-grouping-value';
        $this->stats = 'dummy-stats-value';
        $this->debug = 'dummy-debug-value';

        $this->components = array(
            ComponentAwareQueryInterface::COMPONENT_FACETSET => $this->facetSet,
            ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS => $this->moreLikeThis,
            ComponentAwareQueryInterface::COMPONENT_HIGHLIGHTING => $this->highlighting,
            ComponentAwareQueryInterface::COMPONENT_GROUPING => $this->grouping,
            ComponentAwareQueryInterface::COMPONENT_SPELLCHECK => $this->spellcheck,
            ComponentAwareQueryInterface::COMPONENT_STATS => $this->stats,
            ComponentAwareQueryInterface::COMPONENT_DEBUG => $this->debug,
        );

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
        $this->assertSame(
            null,
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
        $docs = array();
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
