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

class Solarium_Result_Analysis_ItemTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Result_Analysis_Item
     */
    protected $_item;

    protected $_data;

    public function setUp()
    {
        $this->_data = array(
            'text' => 'dummytest',
            'start' => 10,
            'end' => 22,
            'position' => 2,
            'positionHistory' => array(2,1),
            'type' => '<dummytype>',
            'raw_text' => 'dummy raw text',
            'match' => true
        );
        $this->_item = new Solarium_Result_Analysis_Item($this->_data);
    }

    public function testGetText()
    {
        $this->assertEquals($this->_data['text'], $this->_item->getText());
    }

    public function testGetStart()
    {
        $this->assertEquals($this->_data['start'], $this->_item->getStart());
    }

    public function testGetEnd()
    {
        $this->assertEquals($this->_data['end'], $this->_item->getEnd());
    }

    public function testGetPosition()
    {
        $this->assertEquals($this->_data['position'], $this->_item->getPosition());
    }

    public function testGetPositionHistory()
    {
        $this->assertEquals($this->_data['positionHistory'], $this->_item->getPositionHistory());
    }

    public function testGetRawText()
    {
        $this->assertEquals($this->_data['raw_text'], $this->_item->getRawText());
    }

    public function testGetType()
    {
        $this->assertEquals($this->_data['type'], $this->_item->getType());
    }

    public function testGetRawTextEmpty()
    {
        $data = array(
            'text' => 'dummytest',
            'start' => 10,
            'end' => 22,
            'position' => 2,
            'positionHistory' => array(2,1),
            'type' => '<dummytype>',
        );
        $item = new Solarium_Result_Analysis_Item($data);
        $this->assertEquals(null, $item->getRawText());
    }

    public function testGetMatch()
    {
        $this->assertEquals($this->_data['match'], $this->_item->getMatch());
    }
}