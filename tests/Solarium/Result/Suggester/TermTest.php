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

class Solarium_Result_Suggester_TermTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Result_Suggester_Term
     */
    protected $_result;

    /**
     * @var int
     */
    protected $_numFound;

    /**
     * @var int
     */
    protected $_startOffset;

    /**
     * @var int
     */
    protected $_endOffset;

    /**
     * @var array
     */
    protected $_suggestions;

    public function setUp()
    {
        $this->_numFound = 5;
        $this->_startOffset = 2;
        $this->_endOffset = 6;
        $this->_suggestions = array(
            'suggestion1',
            'suggestion2',
        );

        $this->_result = new Solarium_Result_Suggester_Term(
            $this->_numFound, $this->_startOffset, $this->_endOffset, $this->_suggestions
        );
    }

    public function testGetNumFound()
    {
        $this->assertEquals(
            $this->_numFound,
            $this->_result->getNumFound()
        );
    }

    public function testGetStartOffset()
    {
        $this->assertEquals(
            $this->_startOffset,
            $this->_result->getStartOffset()
        );
    }

    public function testGetEndOffset()
    {
        $this->assertEquals(
            $this->_endOffset,
            $this->_result->getEndOffset()
        );
    }

    public function testGetSuggestions()
    {
        $this->assertEquals(
            $this->_suggestions,
            $this->_result->getSuggestions()
        );
    }

    public function testCount()
    {
        $this->assertEquals(count($this->_suggestions), count($this->_result));
    }

    public function testIterator()
    {
        $results = array();
        foreach($this->_result AS $key => $doc)
        {
            $results[$key] = $doc;
        }

        $this->assertEquals($this->_suggestions, $results);
    }

}