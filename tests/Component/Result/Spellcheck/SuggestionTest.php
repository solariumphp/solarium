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

namespace Solarium\Tests\Component\Result\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Spellcheck\Suggestion;

class SuggestionTest extends TestCase
{
    /**
     * @var Suggestion
     */
    protected $result;

    protected $numFound;
    protected $startOffset;
    protected $endOffset;
    protected $originalFrequency;
    protected $words;
    protected $frequency;

    public function setUp()
    {
        $this->numFound = 1;
        $this->startOffset = 2;
        $this->endOffset = 3;
        $this->originalFrequency = 4;
        $this->words = array(
            array(
                'word' => 'dummyword',
                'freq' => 5,
            ),
            array(
                'word' => 'secondword',
                'freq' => 1,
            )
        );

        $this->result = new Suggestion(
            $this->numFound,
            $this->startOffset,
            $this->endOffset,
            $this->originalFrequency,
            $this->words
        );
    }

    public function testGetNumFound()
    {
        $this->assertSame($this->numFound, $this->result->getNumFound());
    }

    public function testGetStartOffset()
    {
        $this->assertSame($this->startOffset, $this->result->getStartOffset());
    }

    public function testGetEndOffset()
    {
        $this->assertSame($this->endOffset, $this->result->getEndOffset());
    }

    public function testGetOriginalFrequency()
    {
         $this->assertSame($this->originalFrequency, $this->result->getOriginalFrequency());
    }

    public function testGetWord()
    {
         $this->assertSame($this->words[0]['word'], $this->result->getWord());
    }

    public function testGetFrequency()
    {
         $this->assertSame($this->words[0]['freq'], $this->result->getFrequency());
    }

    public function testGetWords()
    {
         $this->assertSame($this->words, $this->result->getWords());
    }
}
