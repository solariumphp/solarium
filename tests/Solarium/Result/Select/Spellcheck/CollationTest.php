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

class Solarium_Result_Select_Spellcheck_CollationTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Result_Select_Spellcheck_Collation
     */
    protected $_result;

    protected $_corrections, $_hits, $_query;

    public function setUp()
    {
        $this->_corrections = array(
            'key1' => 'content1',
            'key2' => 'content2',
        );
        $this->_hits = 1;
        $this->_query = 'dummy query';

        $this->_result = new Solarium_Result_Select_Spellcheck_Collation($this->_query, $this->_hits, $this->_corrections);
    }

    public function testGetQuery()
    {
        $this->assertEquals($this->_query, $this->_result->getQuery());
    }

    public function testGetHits()
    {
        $this->assertEquals($this->_hits, $this->_result->getHits());
    }

    public function testGetCorrections()
    {
         $this->assertEquals($this->_corrections, $this->_result->getCorrections());
    }

    public function testIterator()
    {
        $items = array();
        foreach($this->_result AS $key => $item)
        {
            $items[$key] = $item;
        }

        $this->assertEquals($this->_corrections, $items);
    }

    public function testCount()
    {
        $this->assertEquals(count($this->_corrections), count($this->_result));
    }

}
