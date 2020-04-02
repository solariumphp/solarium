<?php

namespace Solarium\Tests\Core\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\PostCreateResult;
use Solarium\Core\Query\Result\Result;
use Solarium\Tests\Integration\TestClientFactory;

class PostCreateResultTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $query = $client->createSelect();
        $query->setQuery('test123');
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $result = new Result($query, $response);

        $event = new PostCreateResult($query, $response, $result);

        $this->assertSame($query, $event->getQuery());
        $this->assertSame($response, $event->getResponse());
        $this->assertSame($result, $event->getResult());
    }
}
