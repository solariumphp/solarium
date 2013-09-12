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

use Solarium\QueryType\Suggester\Result\Term;

class TermTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Term
     */
    protected $result;

    /**
     * @var int
     */
    protected $numFound;

    /**
     * @var int
     */
    protected $startOffset;

    /**
     * @var int
     */
    protected $endOffset;

    /**
     * @var array
     */
    protected $suggestions;

    public function setUp()
    {
        $this->numFound = 5;
        $this->startOffset = 2;
        $this->endOffset = 6;
        $this->suggestions = array(
            'suggestion1',
            'suggestion2',
        );

        $this->result = new Term($this->numFound, $this->startOffset, $this->endOffset, $this->suggestions);
    }

    public function testGetNumFound()
    {
        $this->assertEquals(
            $this->numFound,
            $this->result->getNumFound()
        );
    }

    public function testGetStartOffset()
    {
        $this->assertEquals(
            $this->startOffset,
            $this->result->getStartOffset()
        );
    }

    public function testGetEndOffset()
    {
        $this->assertEquals(
            $this->endOffset,
            $this->result->getEndOffset()
        );
    }

    public function testGetSuggestions()
    {
        $this->assertEquals(
            $this->suggestions,
            $this->result->getSuggestions()
        );
    }

    public function testCount()
    {
        $this->assertEquals(count($this->suggestions), count($this->result));
    }

    public function testIterator()
    {
        $results = array();
        foreach ($this->result as $key => $doc) {
            $results[$key] = $doc;
        }

        $this->assertEquals($this->suggestions, $results);
    }
}
