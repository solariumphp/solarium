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

namespace Solarium\Tests\Plugin\CustomizeRequest;

use Solarium\Plugin\CustomizeRequest\Customization;

class CustomizationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Customization
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new Customization();
    }

    public function testSetAndGetKey()
    {
        $value = 'mykey';
        $this->instance->setKey($value);
        $this->assertEquals($value, $this->instance->getKey());
    }

    public function testSetAndGetName()
    {
        $value = 'myname';
        $this->instance->setName($value);
        $this->assertEquals($value, $this->instance->getName());
    }

    public function testSetAndGetType()
    {
        $value = 'mytype';
        $this->instance->setType($value);
        $this->assertEquals($value, $this->instance->getType());
    }

    public function testSetAndGetValue()
    {
        $value = 'myvalue';
        $this->instance->setValue($value);
        $this->assertEquals($value, $this->instance->getValue());
    }

    public function testSetAndGetPersistence()
    {
        $value = true;
        $this->instance->setPersistent($value);
        $this->assertEquals($value, $this->instance->getPersistent());
    }

    public function testSetAndGetOverwrite()
    {
        $value = false;
        $this->instance->setOverwrite($value);
        $this->assertEquals($value, $this->instance->getOverwrite());
    }

    public function testIsValid()
    {
        $this->instance->setKey('mykey');
        $this->instance->setType('param');
        $this->instance->setName('myname');
        $this->instance->setValue('myvalue');
        $this->assertTrue($this->instance->isValid());
    }

    public function testIsValidWithInvalidType()
    {
        $this->instance->setKey('mykey');
        $this->instance->setType('mytype');
        $this->instance->setName('myname');
        $this->instance->setValue('myvalue');

        $this->assertFalse($this->instance->isValid());
    }

    public function testIsValidWithMissingValue()
    {
        $this->instance->setKey('mykey');
        $this->instance->setType('param');
        $this->instance->setName('myname');

        $this->assertFalse($this->instance->isValid());
    }
}
