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

namespace Solarium\Tests\QueryType\Select\Result\Spellcheck;

use Solarium\QueryType\Select\Result\Spellcheck\Result;

class SpellcheckTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Result
     */
    protected $result;

    protected $suggestions;
    protected $collations;
    protected $correctlySpelled;

    public function setUp()
    {
        $this->suggestions = array(
            'key1' => 'content1',
            'key2' => 'content2',
        );

        $this->collations = array(
            'dummy1',
            'dummy2'
        );
        $this->correctlySpelled = false;

        $this->result = new Result($this->suggestions, $this->collations, $this->correctlySpelled);
    }

    public function testGetCollation()
    {
        $this->assertEquals(reset($this->collations), $this->result->getCollation());
    }

    public function testGetCollationWithoutData()
    {
        $result = new Result($this->suggestions, array(), $this->correctlySpelled);
        $this->assertEquals(null, $result->getCollation());
    }

    public function testGetCollationWithKey()
    {
        $this->assertEquals($this->collations[0], $this->result->getCollation(0));
    }

    public function testGetCollations()
    {
        $this->assertEquals($this->collations, $this->result->getCollations());
    }

    public function testGetCorrectlySpelled()
    {
        $this->assertEquals($this->correctlySpelled, $this->result->getCorrectlySpelled());
    }

    public function testGetSuggestion()
    {
         $this->assertEquals($this->suggestions['key1'], $this->result->getSuggestion('key1'));
    }

    public function testGetInvalidSuggestion()
    {
         $this->assertEquals(null, $this->result->getSuggestion('key3'));
    }

    public function testGetSuggestions()
    {
         $this->assertEquals($this->suggestions, $this->result->getSuggestions());
    }

    public function testIterator()
    {
        $items = array();
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->suggestions, $items);
    }

    public function testCount()
    {
        $this->assertEquals(count($this->suggestions), count($this->result));
    }
}
