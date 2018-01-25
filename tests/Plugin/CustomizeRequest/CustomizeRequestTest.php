<?php

namespace Solarium\Tests\Plugin\CustomizeRequest;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Client\Adapter\Http;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\RuntimeException;
use Solarium\Plugin\CustomizeRequest\Customization;
use Solarium\Plugin\CustomizeRequest\CustomizeRequest;

class CustomizeRequestTest extends TestCase
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
        $options = [
            'customization' => [
                [
                    'key' => 'auth',
                    'type' => 'header',
                    'name' => 'X-my-auth',
                    'value' => 'mypassword',
                    'persistent' => true,
                ],
                'id' => [
                    'type' => 'param',
                    'name' => 'id',
                    'value' => '1234',
                    'persistent' => false,
                    'overwrite' => false,
                ],
            ],
        ];

        $this->plugin->setOptions($options);

        $auth = $this->plugin->getCustomization('auth');
        $id = $this->plugin->getCustomization('id');

        $this->assertThat($auth, $this->isInstanceOf('Solarium\Plugin\CustomizeRequest\Customization'));
        $this->assertSame('auth', $auth->getKey());
        $this->assertSame('header', $auth->getType());
        $this->assertSame('X-my-auth', $auth->getName());
        $this->assertSame('mypassword', $auth->getValue());
        $this->assertTrue($auth->getPersistent());

        $this->assertThat($id, $this->isInstanceOf('Solarium\Plugin\CustomizeRequest\Customization'));
        $this->assertSame('id', $id->getKey());
        $this->assertSame('param', $id->getType());
        $this->assertSame('id', $id->getName());
        $this->assertSame('1234', $id->getValue());
        $this->assertFalse($id->getPersistent());
        $this->assertFalse($id->getOverwrite());
    }

    public function testPluginIntegration()
    {
        $client = new Client();
        $client->registerPlugin('testplugin', $this->plugin);

        $input = [
                    'key' => 'xid',
                    'type' => 'param',
                    'name' => 'xid',
                    'value' => 123,
                ];
        $this->plugin->addCustomization($input);

        $originalRequest = new Request();
        $expectedRequest = new Request();
        $expectedRequest->addParam('xid', 123); // this should be the effect of the customization

        $adapter = $this->createMock(AdapterInterface::class);
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $adapter->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($expectedRequest))
                 ->willReturn($response);
        $client->setAdapter($adapter);

        $client->executeRequest($originalRequest);
    }

    public function testCreateCustomization()
    {
        $customization = $this->plugin->createCustomization('id1');

        $this->assertSame(
            $customization,
            $this->plugin->getCustomization('id1')
        );
    }

    public function testCreateCustomizationWithArray()
    {
        $input = [
            'key' => 'auth',
            'type' => 'header',
            'name' => 'X-my-auth',
            'value' => 'mypassword',
            'persistent' => true,
        ];
        $customization = $this->plugin->createCustomization($input);

        $this->assertSame($customization, $this->plugin->getCustomization('auth'));
        $this->assertSame($input['key'], $customization->getKey());
        $this->assertSame($input['type'], $customization->getType());
        $this->assertSame($input['name'], $customization->getName());
        $this->assertSame($input['value'], $customization->getValue());
        $this->assertSame($input['persistent'], $customization->getPersistent());
    }

    public function testAddAndGetCustomization()
    {
        $customization = new Customization();
        $customization->setKey('id1');
        $this->plugin->addCustomization($customization);

        $this->assertSame(
            $customization,
            $this->plugin->getCustomization('id1')
        );
    }

    public function testAddAndGetCustomizationWithKey()
    {
        $key = 'id1';

        $customization = $this->plugin->createCustomization($key);

        $this->assertSame($key, $customization->getKey());
        $this->assertSame($customization, $this->plugin->getCustomization($key));
    }

    public function testAddCustomizationWithoutKey()
    {
        $customization = new Customization();

        $this->expectException(InvalidArgumentException::class);
        $this->plugin->addCustomization($customization);
    }

    public function testAddCustomizationWithUsedKey()
    {
        $customization1 = new Customization();
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization();
        $customization2->setKey('id1')->setName('test2');

        $this->plugin->addCustomization($customization1);
        $this->expectException(InvalidArgumentException::class);
        $this->plugin->addCustomization($customization2);
    }

    public function testAddDuplicateCustomizationWith()
    {
        $customization = new Customization();
        $customization->setKey('id1')->setName('test1');

        $this->plugin->addCustomization($customization);
        $this->plugin->addCustomization($customization);

        $this->assertSame($customization, $this->plugin->getCustomization('id1'));
    }

    public function testGetInvalidCustomization()
    {
        $this->assertNull($this->plugin->getCustomization('invalidkey'));
    }

    public function testAddCustomizations()
    {
        $customization1 = new Customization();
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization();
        $customization2->setKey('id2')->setName('test2');

        $customizations = ['id1' => $customization1, 'id2' => $customization2];

        $this->plugin->addCustomizations($customizations);
        $this->assertSame($customizations, $this->plugin->getCustomizations());
    }

    public function testRemoveCustomization()
    {
        $customization1 = new Customization();
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization();
        $customization2->setKey('id2')->setName('test2');

        $customizations = [$customization1, $customization2];

        $this->plugin->addCustomizations($customizations);
        $this->plugin->removeCustomization('id1');
        $this->assertSame(
            ['id2' => $customization2],
            $this->plugin->getCustomizations()
        );
    }

    public function testRemoveCustomizationWithObjectInput()
    {
        $customization1 = new Customization();
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization();
        $customization2->setKey('id2')->setName('test2');

        $customizations = [$customization1, $customization2];

        $this->plugin->addCustomizations($customizations);
        $this->plugin->removeCustomization($customization1);
        $this->assertSame(
            ['id2' => $customization2],
            $this->plugin->getCustomizations()
        );
    }

    public function testRemoveInvalidCustomization()
    {
        $customization1 = new Customization();
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization();
        $customization2->setKey('id2')->setName('test2');

        $customizations = ['id1' => $customization1, 'id2' => $customization2];

        $this->plugin->addCustomizations($customizations);
        $this->plugin->removeCustomization('id3'); //continue silently
        $this->assertSame(
            $customizations,
            $this->plugin->getCustomizations()
        );
    }

    public function testClearCustomizations()
    {
        $customization1 = new Customization();
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization();
        $customization2->setKey('id2')->setName('test2');

        $customizations = [$customization1, $customization2];

        $this->plugin->addCustomizations($customizations);
        $this->plugin->clearCustomizations();
        $this->assertSame(
            [],
            $this->plugin->getCustomizations()
        );
    }

    public function testSetCustomizations()
    {
        $customization1 = new Customization();
        $customization1->setKey('id1')->setName('test1');

        $customization2 = new Customization();
        $customization2->setKey('id2')->setName('test2');

        $customizations1 = ['id1' => $customization1, 'id2' => $customization2];

        $this->plugin->addCustomizations($customizations1);

        $customization3 = new Customization();
        $customization3->setKey('id3')->setName('test3');

        $customization4 = new Customization();
        $customization4->setKey('id4')->setName('test4');

        $customizations2 = ['id3' => $customization3, 'id4' => $customization4];

        $this->plugin->setCustomizations($customizations2);

        $this->assertSame($customizations2, $this->plugin->getCustomizations());
    }

    public function testPostCreateRequestWithHeaderAndParam()
    {
        $input = [
                    'key' => 'xid',
                    'type' => 'param',
                    'name' => 'xid',
                    'value' => 123,
                ];
        $this->plugin->addCustomization($input);

        $input = [
                    'key' => 'auth',
                    'type' => 'header',
                    'name' => 'X-my-auth',
                    'value' => 'mypassword',
                    'persistent' => true,
                ];
        $this->plugin->addCustomization($input);

        $request = new Request();
        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(123, $request->getParam('xid'));

        $this->assertEquals(['X-my-auth: mypassword'], $request->getHeaders());
    }

    public function testPreExecuteRequestWithInvalidCustomization()
    {
        $input = [
            'key' => 'xid',
            'type' => 'invalid',
            'name' => 'xid',
            'value' => 123,
        ];
        $this->plugin->addCustomization($input);

        $request = new Request();
        $event = new PreExecuteRequestEvent($request, new Endpoint());

        $this->expectException(RuntimeException::class);
        $this->plugin->preExecuteRequest($event);
    }

    public function testPreExecuteRequestWithoutCustomizations()
    {
        $request = new Request();
        $originalRequest = clone $request;

        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals($originalRequest, $request);
    }

    public function testPreExecuteRequestWithPersistentAndNonPersistentCustomizations()
    {
        $input = [
                    'key' => 'xid',
                    'type' => 'param',
                    'name' => 'xid',
                    'value' => 123,
                ];
        $this->plugin->addCustomization($input);

        $input = [
                    'key' => 'auth',
                    'type' => 'header',
                    'name' => 'X-my-auth',
                    'value' => 'mypassword',
                    'persistent' => true,
                ];
        $this->plugin->addCustomization($input);

        $request = new Request();
        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(123, $request->getParam('xid'));

        $this->assertEquals(['X-my-auth: mypassword'], $request->getHeaders());

        // second use, only the header should be persistent
        $request = new Request();
        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertNull($request->getParam('xid'));

        $this->assertEquals(['X-my-auth: mypassword'], $request->getHeaders());
    }
}
