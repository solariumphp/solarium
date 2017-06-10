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

namespace Solarium\Tests\QueryType\Terms;

use Solarium\QueryType\Terms\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TermsDummy
     */
    protected $result;

    /**
     * @var array
     */
    protected $data;

    public function setUp()
    {
        $this->data = array(
            'fieldA' => array(
                'term1',
                11,
                'term2',
                5,
                'term3',
                2,
            ),
            'fieldB' => array(
                'term4',
                4,
                'term5',
                1,
            )
        );

        $this->result = new TermsDummy($this->data);
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

    public function testGetTerms()
    {
        $this->assertEquals($this->data['fieldA'], $this->result->getTerms('fieldA'));
    }

    public function testGetTermsWithInvalidFieldName()
    {
        $this->assertEquals(array(), $this->result->getTerms('fieldX'));
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
}

class TermsDummy extends Result
{
    protected $parsed = true;

    public function __construct($results)
    {
        $this->results = $results;
        $this->status = 1;
        $this->queryTime = 12;
    }
}
