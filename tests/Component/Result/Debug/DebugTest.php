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

namespace Solarium\Tests\Component\Result\Debug;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Debug\Result;

class DebugTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    protected $queryString;
    protected $queryParser;
    protected $parsedQuery;
    protected $otherQuery;
    protected $explain;
    protected $explainOther;
    protected $explainData;
    protected $timing;

    public function setUp()
    {
        $this->queryString = 'dummy-querystring';
        $this->parsedQuery = 'dummy-parsed-qs';
        $this->queryParser = 'dummy-parser';
        $this->otherQuery = 'id:67';
        $this->explainData = array('a'=>'dummy1', 'b'=>'dummy2');
        $this->explain = new \ArrayIterator($this->explainData);
        $this->explainOther = 'dummy-other';
        $this->timing = 'dummy-timing';

        $this->result = new Result(
            $this->queryString,
            $this->parsedQuery,
            $this->queryParser,
            $this->otherQuery,
            $this->explain,
            $this->explainOther,
            $this->timing
        );
    }

    public function testGetQueryString()
    {
         $this->assertSame($this->queryString, $this->result->getQueryString());
    }

    public function testGetParsedQuery()
    {
         $this->assertSame($this->parsedQuery, $this->result->getParsedQuery());
    }

    public function testGetQueryParser()
    {
         $this->assertSame($this->queryParser, $this->result->getQueryParser());
    }

    public function testGetOtherQuery()
    {
         $this->assertSame($this->otherQuery, $this->result->getOtherQuery());
    }

    public function testGetExplain()
    {
         $this->assertSame($this->explain, $this->result->getExplain());
    }

    public function testGetExplainOther()
    {
         $this->assertSame($this->explainOther, $this->result->getExplainOther());
    }

    public function testGetTiming()
    {
         $this->assertSame($this->timing, $this->result->getTiming());
    }

    public function testIterator()
    {
        $items = array();
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertSame($this->explainData, $items);
    }

    public function testCount()
    {
        $this->assertSame(count($this->explain), count($this->result));
    }
}
