<?php
/**
 * Copyright 2014 Bas de Nooijer. All rights reserved.
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

namespace Solarium\Tests\Plugin\MinimumScoreFilter;

use Solarium\Plugin\MinimumScoreFilter\Query;
use Solarium\Plugin\MinimumScoreFilter\Result;
use Solarium\QueryType\Select\Result\Document;
use Solarium\Tests\QueryType\Select\Result\AbstractResultTest;

class ResultTest extends AbstractResultTest
{
    public function setUp()
    {
        parent::setUp();

        $this->maxScore = 0.91;
        $this->docs = array(
            new Document(array('id'=>1, 'title'=>'doc1', 'score' => 0.91)),
            new Document(array('id'=>2, 'title'=>'doc2', 'score' => 0.654)),
            new Document(array('id'=>3, 'title'=>'doc3', 'score' => 0.23)),
            new Document(array('id'=>4, 'title'=>'doc4', 'score' => 0.08)),
        );

        $this->result = new FilterResultDummy(1, 12, $this->numFound, $this->maxScore, $this->docs, $this->components, Query::FILTER_MODE_MARK);
    }

    public function testIterator()
    {
        foreach ($this->result as $key => $doc) {
            $this->assertEquals($this->docs[$key]->title, $doc->title);
            $this->assertEquals(($key == 3), $doc->markedAsLowScore());
        }
    }

    public function testGetDocuments()
    {
        $this->assertEquals(count($this->docs), count($this->result->getDocuments()));
    }

    public function testIteratorWithRemoveFilter()
    {
        $result = new FilterResultDummy(1, 12, $this->numFound, $this->maxScore, $this->docs, $this->components, Query::FILTER_MODE_REMOVE);
        $docs = array();
        foreach ($result as $key => $doc) {
            $docs[$key] = $doc;
        }

        $this->assertEquals($docs[0]->title, $this->docs[0]->title);
        $this->assertEquals($docs[1]->title, $this->docs[1]->title);
        $this->assertEquals($docs[2]->title, $this->docs[2]->title);
        $this->assertArrayNotHasKey(3, $docs);
    }

    public function testGetDocumentsWithRemoveFilter()
    {
        $result = new FilterResultDummy(1, 12, $this->numFound, $this->maxScore, $this->docs, $this->components, Query::FILTER_MODE_REMOVE);
        $docs = $result->getDocuments();

        $this->assertEquals(3, count($docs));
        $this->assertEquals($docs[0]->title, $this->docs[0]->title);
        $this->assertEquals($docs[1]->title, $this->docs[1]->title);
        $this->assertEquals($docs[2]->title, $this->docs[2]->title);
    }

    public function testFilterWithInvalidMode()
    {
        $this->setExpectedException('Solarium\Exception\OutOfBoundsException');
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
        $this->queryTime = $queryTime;
        $this->status = $status;

        $this->query = new Query();
        $this->query->setFilterRatio(0.2)->setFilterMode($mode);

        $this->mapData(array('documents' => $this->documents, 'maxscore' => $this->maxscore));
    }
}
