<?php

namespace Solarium\Tests\Plugin\BufferedAdd\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Plugin\BufferedAdd\Event\PostFlush;
use Solarium\QueryType\Update\Result;
use Solarium\Tests\Integration\TestClientFactory;

class PostFlushTest extends TestCase
{
    public function testConstructorAndGetter()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $query = $client->createSelect();
        $query->setQuery('test123');
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $result = new Result($query, $response);

        $event = new PostFlush($result);

        $this->assertSame($result, $event->getResult());
    }
}
