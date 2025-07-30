<?php

namespace Solarium\Tests\QueryType\Ping;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Ping\RequestBuilder;
use Solarium\QueryType\Ping\ResponseParser;
use Solarium\QueryType\Ping\Query;

class QueryTest extends TestCase
{
    protected $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_PING, $this->query->getType());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(ResponseParser::class, $this->query->getResponseParser());
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
