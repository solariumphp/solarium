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

class Solarium_Result_Select_Grouping_ValueGroupTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Result_Select_Grouping_ValueGroup
     */
    protected $_group;

    protected $_value, $_numFound, $_start, $_items;

    public function setUp()
    {
        $this->_value = 'test value';
        $this->_numFound = 6;
        $this->_start = 2;

        $this->_items = array(
            'key1' => 'content1',
            'key2' => 'content2',
        );

        $this->_group = new Solarium_Result_Select_Grouping_ValueGroup($this->_value, $this->_numFound, $this->_start, $this->_items);
    }

    public function testGetValue()
    {
        $this->assertEquals(
            $this->_value,
            $this->_group->getValue()
        );
    }

    public function testGetNumFound()
    {
        $this->assertEquals(
            $this->_numFound,
            $this->_group->getNumFound()
        );
    }

    public function testGetStart()
    {
        $this->assertEquals(
            $this->_start,
            $this->_group->getStart()
        );
    }

    public function testGetDocuments()
    {
        $this->assertEquals(
            $this->_items,
            $this->_group->getDocuments()
        );
    }

    public function testIterator()
    {
        $items = array();
        foreach($this->_group AS $key => $item)
        {
            $items[$key] = $item;
        }

        $this->assertEquals($this->_items, $items);
    }

    public function testCount()
    {
        $this->assertEquals(count($this->_items), count($this->_group));
    }

}
