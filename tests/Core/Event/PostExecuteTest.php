<?php

namespace Solarium\Tests\Core\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\PostExecute;
use Solarium\Core\Query\Result\Result;

class PostExecuteTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $client = new Client();
        $query = $client->createSelect();
        $query->setQuery('test123');
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $result = new Result($query, $response);

        $event = new PostExecute($query, $result);

        $this->assertSame($query, $event->getQuery());
        $this->assertSame($result, $event->getResult());
    }
}
