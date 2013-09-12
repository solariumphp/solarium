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

namespace Solarium\Tests\QueryType\Analysis\Result;

use Solarium\QueryType\Analysis\Result\Item;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Item
     */
    protected $item;

    protected $data;

    public function setUp()
    {
        $this->data = array(
            'text' => 'dummytest',
            'start' => 10,
            'end' => 22,
            'position' => 2,
            'positionHistory' => array(2, 1),
            'type' => '<dummytype>',
            'raw_text' => 'dummy raw text',
            'match' => true,
        );
        $this->item = new Item($this->data);
    }

    public function testGetText()
    {
        $this->assertEquals($this->data['text'], $this->item->getText());
    }

    public function testGetStart()
    {
        $this->assertEquals($this->data['start'], $this->item->getStart());
    }

    public function testGetEnd()
    {
        $this->assertEquals($this->data['end'], $this->item->getEnd());
    }

    public function testGetPosition()
    {
        $this->assertEquals($this->data['position'], $this->item->getPosition());
    }

    public function testGetPositionHistory()
    {
        $this->assertEquals($this->data['positionHistory'], $this->item->getPositionHistory());
    }

    public function testGetPositionHistoryFallbackValue()
    {
        $data = $this->data;
        $data['positionHistory'] = '';
        $item = new Item($data);
        $this->assertEquals(array(), $item->getPositionHistory());
    }

    public function testGetRawText()
    {
        $this->assertEquals($this->data['raw_text'], $this->item->getRawText());
    }

    public function testGetType()
    {
        $this->assertEquals($this->data['type'], $this->item->getType());
    }

    public function testGetRawTextEmpty()
    {
        $data = array(
            'text' => 'dummytest',
            'start' => 10,
            'end' => 22,
            'position' => 2,
            'positionHistory' => array(2, 1),
            'type' => '<dummytype>',
        );
        $item = new Item($data);
        $this->assertEquals(null, $item->getRawText());
    }

    public function testGetMatch()
    {
        $this->assertEquals($this->data['match'], $this->item->getMatch());
    }
}
