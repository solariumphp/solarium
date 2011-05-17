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

class Solarium_Plugin_AbstractTest extends PHPUnit_Framework_TestCase
{
    protected $_plugin, $_client, $_options;

    public function setUp()
    {
        $this->_client = 'dummy';
        $this->_options = array('option1' => 1);
        $this->_plugin = new MyPlugin();
        $this->_plugin->init($this->_client, $this->_options);
    }

    public function testConstructor()
    {
       $this->assertEquals(
            $this->_client,
            $this->_plugin->getClient()
        );

        $this->assertEquals(
            $this->_options,
            $this->_plugin->getOptions()
        );
    }


    public function testEventHooksEmpty()
    {
        $this->assertEquals(null, $this->_plugin->preCreateRequest(null));
        $this->assertEquals(null, $this->_plugin->postCreateRequest(null,null));
        $this->assertEquals(null, $this->_plugin->preExecuteRequest(null));
        $this->assertEquals(null, $this->_plugin->postExecuteRequest(null,null));
        $this->assertEquals(null, $this->_plugin->preExecute(null));
        $this->assertEquals(null, $this->_plugin->postExecute(null,null));
        $this->assertEquals(null, $this->_plugin->preCreateResult(null,null));
        $this->assertEquals(null, $this->_plugin->postCreateResult(null,null,null));
        $this->assertEquals(null, $this->_plugin->preCreateQuery(null,null));
        $this->assertEquals(null, $this->_plugin->postCreateQuery(null,null,null));
    }

}

class MyPlugin extends Solarium_Plugin_Abstract{

    public function getClient()
    {
        return $this->_client;
    }

}