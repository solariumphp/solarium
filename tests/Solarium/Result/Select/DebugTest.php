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

class Solarium_Result_Select_DebugTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Result_Select_Debug
     */
    protected $_result;

    protected $_queryString, $_queryParser, $_otherQuery, $_explain, $_explainOther, $timing;

    public function setUp()
    {
        $this->_queryString = 'dummy-querystring';
        $this->_parsedQuery = 'dummy-parsed-qs';
        $this->_queryParser = 'dummy-parser';
        $this->_otherQuery = 'id:67';
        $this->_explainData = array('a'=>'dummy1', 'b'=>'dummy2');
        $this->_explain = new ArrayIterator($this->_explainData);
        $this->_explainOther = 'dummy-other';
        $this->_timing = 'dummy-timing';

        $this->_result = new Solarium_Result_Select_Debug(
            $this->_queryString,
            $this->_parsedQuery,
            $this->_queryParser,
            $this->_otherQuery,
            $this->_explain,
            $this->_explainOther,
            $this->_timing
        );
    }

    public function testGetQueryString()
    {
         $this->assertEquals($this->_queryString, $this->_result->getQueryString());
    }

    public function testGetParsedQuery()
    {
         $this->assertEquals($this->_parsedQuery, $this->_result->getParsedQuery());
    }

    public function testGetQueryParser()
    {
         $this->assertEquals($this->_queryParser, $this->_result->getQueryParser());
    }

    public function testGetOtherQuery()
    {
         $this->assertEquals($this->_otherQuery, $this->_result->getOtherQuery());
    }

    public function testGetExplain()
    {
         $this->assertEquals($this->_explain, $this->_result->getExplain());
    }

    public function testGetExplainOther()
    {
         $this->assertEquals($this->_explainOther, $this->_result->getExplainOther());
    }

    public function testGetTiming()
    {
         $this->assertEquals($this->_timing, $this->_result->getTiming());
    }

    public function testIterator()
    {
        $items = array();
        foreach($this->_result AS $key => $item)
        {
            $items[$key] = $item;
        }

        $this->assertEquals($this->_explainData, $items);
    }

    public function testCount()
    {
        $this->assertEquals(count($this->_explain), count($this->_result));
    }

}
