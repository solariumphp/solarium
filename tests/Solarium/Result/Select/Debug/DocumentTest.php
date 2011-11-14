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

class Solarium_Result_Select_Debug_DocumentTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Result_Select_Debug_Document
     */
    protected $_result;

    protected $_value, $_match, $_description, $_key, $_details;

    public function setUp()
    {
        $this->_key = 'dummy-key';
        $this->_value = 1.5;
        $this->_match = true;
        $this->_description = 'dummy-desc';
        $this->_details = array(0 => 'dummy1', 1 => 'dummy2');

        $this->_result = new Solarium_Result_Select_Debug_Document(
            $this->_key,
            $this->_match,
            $this->_value,
            $this->_description,
            $this->_details
        );
    }

    public function testGetKey()
    {
         $this->assertEquals($this->_key, $this->_result->getKey());
    }

    public function testGetDetails()
    {
         $this->assertEquals($this->_details, $this->_result->getDetails());
    }

    public function testIterator()
    {
        $items = array();
        foreach($this->_result AS $key => $item)
        {
            $items[$key] = $item;
        }

        $this->assertEquals($this->_details, $items);
    }

    public function testCount()
    {
        $this->assertEquals(count($this->_details), count($this->_result));
    }

}
