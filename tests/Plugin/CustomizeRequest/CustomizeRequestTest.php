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

use Solarium\Plugin\CustomizeRequest\CustomizeRequest;
use Solarium\Plugin\CustomizeRequest\Customization;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;

class CustomizeRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomizeRequest
     */
    protected $plugin;

    public function setUp()
    {
        $this->plugin = new CustomizeRequest();
    }

    public function testConfigMode()
    {
        $options = array(
            'customization' => array(
                array(
                    'key' => 'auth',
                    'type' => 'header',
                    'name' => 'X-my-auth',
                    'value' => 'mypassword',
                    'persistent' => true,
                ),
                'id' => array(
                    'type' => 'param',
                    'name' => 'id',
                    'value' => '1234',
                    'persistent' => false,
                    'overwrite' => false,
                ),
            )
        );

        $this->plugin->setOptions($options);

        $auth = $this->plugin->getCustomization('auth');
        $id = $this->plugin->getCustomization('id');

        $this->assertThat($auth, $this->isInstanceOf('Solarium\Plugin\CustomizeRequest\Customization'));
        $this->assertEquals('auth', $auth->getKey());
        $this->assertEquals('header', $auth->getType());
        $this->assertEquals('X-my-auth', $auth->getName());
        $this->assertEquals('mypassword', $auth->getValue());
        $this->assertEquals(true, $auth->getPersistent());

        $this->assertThat($id, $this->isInstanceOf('Solarium\Plugin\CustomizeRequest\Customization'));
        $this->assertEquals('id', $id->getKey());
        $this->assertEquals('param', $id->getType());
        $this->assertEquals('id', $id->getName());
        $this->assertEquals('1234', $id->getValue());
        $this->assertEquals(false, $id->getPersistent());
        $this->assertEquals(false, $id->getOverwrite());
    }

    public function testPluginIntegration()
    {
        $client = new Client;
        $client->registerPlugin('testplugin', $this->plugin);

        $input = array(
                    'key' => 'xid',
                    'type' => 'param',
                    'name' => 'xid',
                    'value' => 123,
                );
        $this->plugin->addCustomization($input);

        $originalRequest = new Request();
        $expectedRequest = new Request();
        $expectedRequest->addParam('xid', 123); // this should be the effect of the customization

        $observer = $this->getMock('Solarium\Core\Client\Adapter\Http', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($expectedRequest))
                 ->will($this->returnValue(new Response('', array('HTTP 1.0 200 OK'))));
        $client->setAdapter($observer);

        $client->executeRequest($originalRequest);
    }

    public function testCreateCustomization()
    {
        $customization = $this->plugin->createCustomization('id1');

        $this->assertEquals(
            $customization,
            $this->plugin->getCustomization('id1')
        );
    }

    public function testCreateCustomizationWithArray()
    {
        $input = array(
            'key' => 'auth',
            'type' => 'header',
            'name' => 'X-my-auth',
            'value' => 'mypassword',
            'persistent' => true,
        );
        $customization = $this->plugin->createCustomization($input);

        $this->assertEquals($customization, $this->plugin->getCustomization('auth'));
        $this->assertEquals($input['key'], $customization->getKey());
        $this->assertEquals($input['type'], $customization->getType());
        $this->assertEquals($input['name'], $customization->getName());
        $this->assertEquals($input['value'], $customization->getValue());
        $this->assertEquals($input['persistent'], $customization->getPersistent());
    }

    public function testAddAndGetCustomization()
    {
        $customization = new Customization;
        $customization->setKey('id1');
        $this->plugin->addCustomization($customization);

        $this->assertEquals(
            $customization,
            $this->plugin->getCustomization('id1')
        );
    }

    public function testAddAndGetCustomizationWithKey()
    {
        $key = 'id1';

        $customization = $this->plugin->createCustomization($key);

        $this->assertEquals(
            $key,
            $customization->getKey()
        );

        $this->assertEquals(
            $customization,
            $this->plugin->getCustomization($key)
        );
    }

    public function testAddCustomizationWithoutKey()
    {
        $customization = new Customization;

        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->plugin->addCustomization($customization);
    }

    public function testAddCustomizationWithUsedKey()
    {
        $customization1 = new Customization;
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization;
        $customization2->setKey('id1')->setName('test2');

        $this->plugin->addCustomization($customization1);
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->plugin->addCustomization($customization2);
    }

    public function testAddDuplicateCustomizationWith()
    {
        $customization = new Customization;
        $customization->setKey('id1')->setName('test1');

        $this->plugin->addCustomization($customization);
        $this->plugin->addCustomization($customization);

        $this->assertEquals(
            $customization,
            $this->plugin->getCustomization('id1')
        );
    }

    public function testGetInvalidCustomization()
    {
        $this->assertEquals(
            null,
            $this->plugin->getCustomization('invalidkey')
        );
    }

    public function testAddCustomizations()
    {
        $customization1 = new Customization;
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization;
        $customization2->setKey('id2')->setName('test2');

        $customizations = array('id1' => $customization1, 'id2' => $customization2);

        $this->plugin->addCustomizations($customizations);
        $this->assertEquals(
            $customizations,
            $this->plugin->getCustomizations()
        );
    }

    public function testRemoveCustomization()
    {
        $customization1 = new Customization;
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization;
        $customization2->setKey('id2')->setName('test2');

        $customizations = array($customization1, $customization2);

        $this->plugin->addCustomizations($customizations);
        $this->plugin->removeCustomization('id1');
        $this->assertEquals(
            array('id2' => $customization2),
            $this->plugin->getCustomizations()
        );
    }

    public function testRemoveCustomizationWithObjectInput()
    {
        $customization1 = new Customization;
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization;
        $customization2->setKey('id2')->setName('test2');

        $customizations = array($customization1, $customization2);

        $this->plugin->addCustomizations($customizations);
        $this->plugin->removeCustomization($customization1);
        $this->assertEquals(
            array('id2' => $customization2),
            $this->plugin->getCustomizations()
        );
    }

    public function testRemoveInvalidCustomization()
    {
        $customization1 = new Customization;
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization;
        $customization2->setKey('id2')->setName('test2');

        $customizations = array('id1' => $customization1, 'id2' => $customization2);

        $this->plugin->addCustomizations($customizations);
        $this->plugin->removeCustomization('id3'); //continue silently
        $this->assertEquals(
            $customizations,
            $this->plugin->getCustomizations()
        );
    }

    public function testClearCustomizations()
    {
        $customization1 = new Customization;
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization;
        $customization2->setKey('id2')->setName('test2');

        $customizations = array($customization1, $customization2);

        $this->plugin->addCustomizations($customizations);
        $this->plugin->clearCustomizations();
        $this->assertEquals(
            array(),
            $this->plugin->getCustomizations()
        );
    }

    public function testSetCustomizations()
    {
        $customization1 = new Customization;
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization;
        $customization2->setKey('id2')->setName('test2');

        $customizations1 = array('id1' => $customization1, 'id2' => $customization2);

        $this->plugin->addCustomizations($customizations1);

        $customization3 = new Customization;
        $customization3->setKey('id3')->setName('test3');

        $customization4 = new Customization;
        $customization4->setKey('id4')->setName('test4');

        $customizations2 = array('id3' => $customization3, 'id4' => $customization4);

        $this->plugin->setCustomizations($customizations2);

        $this->assertEquals(
            $customizations2,
            $this->plugin->getCustomizations()
        );
    }

    public function testPostCreateRequestWithHeaderAndParam()
    {
        $input = array(
                    'key' => 'xid',
                    'type' => 'param',
                    'name' => 'xid',
                    'value' => 123,
                );
        $this->plugin->addCustomization($input);

        $input = array(
                    'key' => 'auth',
                    'type' => 'header',
                    'name' => 'X-my-auth',
                    'value' => 'mypassword',
                    'persistent' => true,
                );
        $this->plugin->addCustomization($input);

        $request = new Request();
        $event = new PreExecuteRequestEvent($request, new Endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals(
            123,
            $request->getParam('xid')
        );

        $this->assertEquals(
            array('X-my-auth: mypassword'),
            $request->getHeaders()
        );
    }

    public function testPreExecuteRequestWithInvalidCustomization()
    {
        $input = array(
            'key' => 'xid',
            'type' => 'invalid',
            'name' => 'xid',
            'value' => 123,
        );
        $this->plugin->addCustomization($input);

        $request = new Request();
        $event = new PreExecuteRequestEvent($request, new Endpoint);

        $this->setExpectedException('Solarium\Exception\RuntimeException');
        $this->plugin->preExecuteRequest($event);
    }

    public function testPreExecuteRequestWithoutCustomizations()
    {
        $request = new Request();
        $originalRequest = clone $request;

        $event = new PreExecuteRequestEvent($request, new Endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals(
            $originalRequest,
            $request
        );
    }

    public function testPreExecuteRequestWithPersistentAndNonPersistentCustomizations()
    {
        $input = array(
                    'key' => 'xid',
                    'type' => 'param',
                    'name' => 'xid',
                    'value' => 123,
                );
        $this->plugin->addCustomization($input);

        $input = array(
                    'key' => 'auth',
                    'type' => 'header',
                    'name' => 'X-my-auth',
                    'value' => 'mypassword',
                    'persistent' => true,
                );
        $this->plugin->addCustomization($input);

        $request = new Request();
        $event = new PreExecuteRequestEvent($request, new Endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals(
            123,
            $request->getParam('xid')
        );

        $this->assertEquals(
            array('X-my-auth: mypassword'),
            $request->getHeaders()
        );

        // second use, only the header should be persistent
        $request = new Request();
        $event = new PreExecuteRequestEvent($request, new Endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals(
            null,
            $request->getParam('xid')
        );

        $this->assertEquals(
            array('X-my-auth: mypassword'),
            $request->getHeaders()
        );
    }
}
