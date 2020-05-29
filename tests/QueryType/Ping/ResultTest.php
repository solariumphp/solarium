<?php

namespace Solarium\Tests\QueryType\Ping;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Ping\Query;
use Solarium\QueryType\Ping\Result;
use Solarium\Tests\Integration\TestClientFactory;

class ResultTest extends TestCase
{
    public function testGetStatus()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $query = new Query();
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', ['HTTP 1.1 200 OK']);

        $ping = new Result($query, $response);
        $this->assertSame(
            0,
            $ping->getStatus()
        );
    }
}
