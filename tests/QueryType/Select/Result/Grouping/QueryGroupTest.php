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

namespace Solarium\Tests\QueryType\Select\Result\Grouping;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Grouping\QueryGroup;

class QueryGroupTest extends TestCase
{
    /**
     * @var QueryGroup
     */
    protected $group;

    protected $matches;
    protected $numFound;
    protected $start;
    protected $maximumScore;
    protected $items;

    public function setUp()
    {
        $this->matches = 12;
        $this->numFound = 6;
        $this->start = 2;
        $this->maximumScore = 0.89;

        $this->items = array(
            'key1' => 'content1',
            'key2' => 'content2',
        );

        $this->group = new QueryGroup($this->matches, $this->numFound, $this->start, $this->maximumScore, $this->items);
    }

    public function testGetMatches()
    {
        $this->assertSame(
            $this->matches,
            $this->group->getMatches()
        );
    }

    public function testGetNumFound()
    {
        $this->assertSame(
            $this->numFound,
            $this->group->getNumFound()
        );
    }

    public function testGetStart()
    {
        $this->assertSame(
            $this->start,
            $this->group->getStart()
        );
    }

    public function testGetMaximumScore()
    {
        $this->assertSame(
            $this->maximumScore,
            $this->group->getMaximumScore()
        );
    }

    public function testGetDocuments()
    {
        $this->assertSame(
            $this->items,
            $this->group->getDocuments()
        );
    }

    public function testIterator()
    {
        $items = array();
        foreach ($this->group as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertSame($this->items, $items);
    }

    public function testCount()
    {
        $this->assertSame(count($this->items), count($this->group));
    }
}
