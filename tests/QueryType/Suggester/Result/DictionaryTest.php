<?php
/**
 * Copyright 2011 Markus Kalkbrenner. All rights reserved.
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

use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Term;

class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Term[]
     */
    protected $terms;

    /**
     * @var Dictionary
     */
    protected $dictionary;

    public function setUp()
    {
        $this->terms = [
            'foo' => new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
            'zoo' => new Term(1, [['term' => 'zoo keeper']]),
        ];

        $this->dictionary = new Dictionary($this->terms);
    }

    public function testGetTerms()
    {
        $this->assertEquals(
            $this->terms,
            $this->dictionary->getTerms()
        );
    }

    public function testGetTerm()
    {
        $this->assertEquals(
            $this->terms['zoo'],
            $this->dictionary->getTerm('zoo')
        );
    }

    public function testGetTermWithUnknownKey()
    {
        $this->assertEquals(
            null,
            $this->dictionary->getTerm('bar')
        );
    }

    public function testCount()
    {
        $this->assertEquals(count($this->terms), count($this->dictionary));
    }

    public function testIterator()
    {
        $results = array();
        foreach ($this->dictionary as $key => $doc) {
            $results[$key] = $doc;
        }

        $this->assertEquals($this->terms, $results);
    }
}
