<?php

namespace Solarium\Tests\Plugin\BufferedDelete\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Plugin\BufferedDelete\Event\PostFlush;
use Solarium\QueryType\Update\Result;
use Solarium\Tests\Integration\TestClientFactory;

class PostFlushTest extends TestCase
{
    public function testConstructorAndGetter()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $query = $client->createUpdate();
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $result = new Result($query, $response);

        $event = new PostFlush($result);

        $this->assertSame($result, $event->getResult());
    }
}
