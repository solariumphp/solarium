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

class Solarium_Plugin_CustomizeRequest_CustomizationTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Plugin_CustomizeRequest_Customization
     */
    protected $_instance;

    public function setUp()
    {
        $this->_instance = new Solarium_Plugin_CustomizeRequest_Customization();
    }

    public function testSetAndGetKey()
    {
        $value = 'mykey';
        $this->_instance->setKey($value);
        $this->assertEquals($value, $this->_instance->getKey());
    }

    public function testSetAndGetName()
    {
        $value = 'myname';
        $this->_instance->setName($value);
        $this->assertEquals($value, $this->_instance->getName());
    }

    public function testSetAndGetType()
    {
        $value = 'mytype';
        $this->_instance->setType($value);
        $this->assertEquals($value, $this->_instance->getType());
    }

    public function testSetAndGetValue()
    {
        $value = 'myvalue';
        $this->_instance->setValue($value);
        $this->assertEquals($value, $this->_instance->getValue());
    }

    public function testSetAndGetPersistence()
    {
        $value = true;
        $this->_instance->setPersistent($value);
        $this->assertEquals($value, $this->_instance->getPersistent());
    }

    public function testSetAndGetOverwrite()
    {
        $value = false;
        $this->_instance->setOverwrite($value);
        $this->assertEquals($value, $this->_instance->getOverwrite());
    }

    public function testIsValid()
    {
        $this->_instance->setKey('mykey');
        $this->_instance->setType('param');
        $this->_instance->setName('myname');
        $this->_instance->setValue('myvalue');
        $this->assertTrue($this->_instance->isValid());
    }

    public function testIsValidWithInvalidType()
    {
        $this->_instance->setKey('mykey');
        $this->_instance->setType('mytype');
        $this->_instance->setName('myname');
        $this->_instance->setValue('myvalue');

        $this->assertFalse($this->_instance->isValid());
    }

    public function testIsValidWithMissingValue()
    {
        $this->_instance->setKey('mykey');
        $this->_instance->setType('param');
        $this->_instance->setName('myname');

        $this->assertFalse($this->_instance->isValid());
    }

}