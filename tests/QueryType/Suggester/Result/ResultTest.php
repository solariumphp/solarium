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

namespace Solarium\Tests\QueryType\Suggester\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Result;

class ResultTest extends TestCase
{
    /**
     * @var SuggesterDummy
     */
    protected $result;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $allData;

    public function setUp()
    {
        $this->data = [
            'dictionary1' => new Dictionary([
                'term1' => 'data1',
                'term2' => 'data2',
            ]),
            'dictionary2' => new Dictionary([
                'term3' => 'data3',
            ]),
        ];
        $this->allData = ['data1', 'data2', 'data3'];
        $this->result = new SuggesterDummy($this->data, $this->allData);
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

    public function testGetResults()
    {
        $this->assertSame($this->data, $this->result->getResults());
    }

    public function testGetAll()
    {
        $this->assertSame($this->allData, $this->result->getAll());
    }

    public function testGetDictionary()
    {
        $dictionary = $this->result->getDictionary('dictionary1');
        $this->assertSame('data1', $dictionary->getTerm('term1'));
    }

    public function testGetDictionaryWithInvalidFieldName()
    {
        $this->assertSame(null, $this->result->getDictionary('dictionary3'));
    }

    public function testCount()
    {
        $this->assertSame(count($this->data), count($this->result));
    }

    public function testIterator()
    {
        $results = array();
        foreach ($this->result as $key => $doc) {
            $results[$key] = $doc;
        }

        $this->assertSame($this->data, $results);
    }
}

class SuggesterDummy extends Result
{
    protected $parsed = true;

    public function __construct($results, $all)
    {
        $this->results = $results;
        $this->all = $all;
        $this->status = 1;
        $this->queryTime = 12;
    }
}
