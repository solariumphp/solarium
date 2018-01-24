<?php

namespace Solarium\Tests\Plugin;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\PostCreateRequest as PostCreateRequestEvent;
use Solarium\Plugin\PostBigRequest;
use Solarium\QueryType\Select\Query\Query;

class PostBigRequestTest extends TestCase
{
    /**
     * @var PostBigRequest
     */
    protected $plugin;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Query
     */
    protected $query;

    public function setUp()
    {
        $this->plugin = new PostBigRequest();

        $this->client = new Client();
        $this->query = $this->client->createSelect();
    }

    public function testSetAndGetMaxQueryStringLength()
    {
        $this->plugin->setMaxQueryStringLength(512);
        $this->assertSame(512, $this->plugin->getMaxQueryStringLength());
    }

    public function testPostCreateRequest()
    {
        // create a very long query
        $fq = '';
        for ($i = 1; $i <= 1000; ++$i) {
            $fq .= ' OR price:'.$i;
        }
        $fq = substr($fq, 4);
        $this->query->createFilterQuery('fq')->setQuery($fq);

        $requestOutput = $this->client->createRequest($this->query);
        $requestInput = clone $requestOutput;
        $event = new PostCreateRequestEvent($this->query, $requestOutput);
        $this->plugin->postCreateRequest($event);

        $this->assertSame(Request::METHOD_GET, $requestInput->getMethod());
        $this->assertSame(Request::METHOD_POST, $requestOutput->getMethod());
        $this->assertSame($requestInput->getQueryString(), $requestOutput->getRawData());
        $this->assertSame('', $requestOutput->getQueryString());
    }

    public function testPostCreateRequestUnalteredSmallRequest()
    {
        $requestOutput = $this->client->createRequest($this->query);
        $requestInput = clone $requestOutput;
        $event = new PostCreateRequestEvent($this->query, $requestOutput);
        $this->plugin->postCreateRequest($event);

        $this->assertEquals($requestInput, $requestOutput);
    }

    public function testPostCreateRequestUnalteredPostRequest()
    {
        $query = $this->client->createUpdate();
        $query->addDeleteById(1);

        $requestOutput = $this->client->createRequest($query);
        $requestInput = clone $requestOutput;
        $event = new PostCreateRequestEvent($this->query, $requestOutput);
        $this->plugin->postCreateRequest($event);

        $this->assertEquals($requestInput, $requestOutput);
    }

    public function testPluginIntegration()
    {
        $client = new Client();
        $client->registerPlugin('testplugin', $this->plugin);
        $this->plugin->setMaxQueryStringLength(1); // this forces POST for even the smallest queries

        $query = $client->createSelect();
        $request = $client->createRequest($query);

        // default method is GET, the plugin should have changed this to POST
        $this->assertSame(Request::METHOD_POST, $request->getMethod());
    }
}
