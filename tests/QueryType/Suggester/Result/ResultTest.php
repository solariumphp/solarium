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

use Solarium\QueryType\Suggester\Result\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @var string
     */
    protected $collation;

    public function setUp()
    {
        $this->data = array(
            'term1' => 'data1',
            'term2' => 'data2',
        );
        $this->allData = array_values($this->data);
        $this->collation = 'collation result';
        $this->result = new SuggesterDummy($this->data, $this->allData, $this->collation);
    }

    public function testGetStatus()
    {
        $this->assertEquals(
            1,
            $this->result->getStatus()
        );
    }

    public function testGetQueryTime()
    {
        $this->assertEquals(
            12,
            $this->result->getQueryTime()
        );
    }

    public function testGetResults()
    {
        $this->assertEquals($this->data, $this->result->getResults());
    }

    public function testGetAll()
    {
        $this->assertEquals($this->allData, $this->result->getAll());
    }

    public function testGetTerm()
    {
        $this->assertEquals($this->data['term1'], $this->result->getTerm('term1'));
    }

    public function testGetTermsWithInvalidFieldName()
    {
        $this->assertEquals(array(), $this->result->getTerm('term3'));
    }

    public function testCount()
    {
        $this->assertEquals(count($this->data), count($this->result));
    }

    public function testIterator()
    {
        $results = array();
        foreach ($this->result as $key => $doc) {
            $results[$key] = $doc;
        }

        $this->assertEquals($this->data, $results);
    }

    public function testGetCollation()
    {
        $this->assertEquals($this->collation, $this->result->getCollation());
    }
}

class SuggesterDummy extends Result
{
    protected $parsed = true;

    public function __construct($results, $all, $collation)
    {
        $this->results = $results;
        $this->all = $all;
        $this->collation = $collation;
        $this->status = 1;
        $this->queryTime = 12;
    }
}
