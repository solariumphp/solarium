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

namespace Solarium\Tests\Core\Plugin;

use Solarium\Core\Plugin\AbstractPlugin;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractPlugin
     */
    protected $plugin;

    protected $client;
    protected $options;

    public function setUp()
    {
        $this->client = 'dummy';
        $this->options = array('option1' => 1);
        $this->plugin = new MyPlugin();
        $this->plugin->initPlugin($this->client, $this->options);
    }

    public function testConstructor()
    {
        $this->assertEquals($this->client, $this->plugin->getClient());
        $this->assertEquals($this->options, $this->plugin->getOptions());
    }

    public function testEventHooksEmpty()
    {
        $this->markTestSkipped('This test is currently skipped for unknown reasons.');

        $this->assertEquals(null, $this->plugin->preCreateRequest(null));
        $this->assertEquals(null, $this->plugin->postCreateRequest(null, null));
        $this->assertEquals(null, $this->plugin->preExecuteRequest(null));
        $this->assertEquals(null, $this->plugin->postExecuteRequest(null, null));
        $this->assertEquals(null, $this->plugin->preExecute(null));
        $this->assertEquals(null, $this->plugin->postExecute(null, null));
        $this->assertEquals(null, $this->plugin->preCreateResult(null, null));
        $this->assertEquals(null, $this->plugin->postCreateResult(null, null, null));
        $this->assertEquals(null, $this->plugin->preCreateQuery(null, null));
        $this->assertEquals(null, $this->plugin->postCreateQuery(null, null, null));
    }
}

class MyPlugin extends AbstractPlugin
{
    public function getClient()
    {
        return $this->client;
    }
}
