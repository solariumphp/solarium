<?php

namespace Solarium\Tests\Core\Client;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\HttpException;

class HttpExceptionTest extends TestCase
{
    public function testConstructor()
    {
        $exception = new HttpException('message text', 123);

        $this->assertSame(
            'Solr HTTP error: message text (123)',
            $exception->getMessage()
        );
    }

    public function testGetMessage()
    {
        $exception = new HttpException('message text', 123);

        $this->assertSame(
            'message text',
            $exception->getStatusMessage()
        );
    }

    public function testGetBody()
    {
        $exception = new HttpException('message text', 123, 'body text');

        $this->assertSame(
            'body text',
            $exception->getBody()
        );
    }

    public function testConstructorNoCodeOrBody()
    {
        $exception = new HttpException('message text');

        $this->assertSame(
            'Solr HTTP error: message text',
            $exception->getMessage()
        );
    }
}
