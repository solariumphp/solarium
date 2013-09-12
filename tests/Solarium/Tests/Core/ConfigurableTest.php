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

namespace Solarium\Tests\Core;

use Solarium\Core\Client\Client;
use Solarium\Exception\RuntimeException;
use Solarium\Core\Configurable;

class ConfigurableTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorNoConfig()
    {
        $configTest = new ConfigTest;
        $defaultOptions = array(
            'option1' => 1,
            'option2' => 'value 2',
        );

        $this->assertEquals($configTest->getOptions(), $defaultOptions);
    }

    public function testConstructorWithObject()
    {
        $configTest = new ConfigTest(new MyConfigObject);

        // the default options should be merged with the constructor values,
        // overwriting any default values.
        $expectedOptions = array(
            'option1' => 1,
            'option2' => 'newvalue2',
            'option3' => 3,
        );

        $this->assertEquals($expectedOptions, $configTest->getOptions());
    }

    public function testConstructorWithArrayConfig()
    {
        $configTest = new ConfigTest(
            array('option2' => 'newvalue2', 'option3' => 3)
        );

        // the default options should be merged with the constructor values,
        // overwriting any default values.
        $expectedOptions = array(
            'option1' => 1,
            'option2' => 'newvalue2',
            'option3' => 3,
        );

        $this->assertEquals($expectedOptions, $configTest->getOptions());
    }

    public function testConstructorWithInvalidConfig()
    {
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        new Client('invalid');
    }

    public function testGetOption()
    {
        $configTest = new ConfigTest;
        $this->assertEquals(1, $configTest->getOption('option1'));
    }

    public function testGetOptionWIthInvalidName()
    {
        $configTest = new ConfigTest();
        $this->assertEquals(null, $configTest->getOption('invalidoptionname'));
    }

    public function testInitialisation()
    {
        $this->setExpectedException('Solarium\Exception\RuntimeException');
        new ConfigTestInit;
    }

    public function testSetOptions()
    {
        $configTest = new ConfigTest();
        $configTest->setOptions(array('option2' => 2, 'option3' => 3));

        $this->assertEquals(
            array('option1' => 1, 'option2' => 2, 'option3' => 3),
            $configTest->getOptions()
        );
    }

    public function testSetOptionsWithOverride()
    {
        $configTest = new ConfigTest();
        $configTest->setOptions(array('option2' => 2, 'option3' => 3), true);

        $this->assertEquals(
            array('option2' => 2, 'option3' => 3),
            $configTest->getOptions()
        );
    }
}

class ConfigTest extends Configurable
{
    protected $options = array(
        'option1' => 1,
        'option2' => 'value 2',
    );
}

class ConfigTestInit extends ConfigTest
{
    protected function init()
    {
        throw new RuntimeException('test init');
    }
}

class MyConfigObject
{
    public function toArray()
    {
        return array('option2' => 'newvalue2', 'option3' => 3);
    }
}
