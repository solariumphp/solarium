<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\HttpException;

class HttpExceptionTest extends TestCase
{
    public function testException()
    {
        $this->expectException('Solarium\Exception\HttpException');
        throw new HttpException('message text');
    }

    public function testSPLParentException()
    {
        $this->expectException('\RuntimeException');
        throw new HttpException('message text');
    }

    public function testRuntimeMarkerInterface()
    {
        $this->expectException('Solarium\Exception\RuntimeExceptionInterface');
        throw new HttpException('message text');
    }

    public function testMarkerInterface()
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new HttpException('message text');
    }

    public function testConstructor()
    {
        $exception = new HttpException('message text');

        $this->assertSame(
            'Solr HTTP error: message text',
            $exception->getMessage()
        );

        $exception = new HttpException('message text', 123);

        $this->assertSame(
            'Solr HTTP error: message text (123)',
            $exception->getMessage()
        );

        $exception = new HttpException('message text', 123, '');

        $this->assertSame(
            'Solr HTTP error: message text (123)',
            $exception->getMessage()
        );

        $exception = new HttpException('message text', 123, 'body text');

        $this->assertSame(
            "Solr HTTP error: message text (123)\nbody text",
            $exception->getMessage()
        );
    }

    public function testGetStatusMessage()
    {
        $exception = new HttpException('message text', 123);

        $this->assertSame(
            'message text',
            $exception->getStatusMessage()
        );
    }

    public function testGetBody()
    {
        $exception = new HttpException('message text', 123);

        $this->assertNull($exception->getBody());

        $exception = new HttpException('message text', 123, 'body text');

        $this->assertSame(
            'body text',
            $exception->getBody()
        );
    }
}
