<?php

namespace Solarium\Tests\QueryType\Ping;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Ping\Query;

class QueryTest extends TestCase
{
    protected $query;

    public function setUp()
    {
        $this->query = new Query();
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_PING, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertNull($this->query->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\Ping\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testConfigMode()
    {
        $options = [
            'handler' => 'myHandler',
            'resultclass' => 'myResult',
        ];
        $this->query->setOptions($options);

        $this->assertSame(
            $options['handler'],
            $this->query->getHandler()
        );

        $this->assertSame(
            $options['resultclass'],
            $this->query->getResultClass()
        );
    }
}
