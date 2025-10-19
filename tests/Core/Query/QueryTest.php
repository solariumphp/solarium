<?php

namespace Solarium\Tests\Core\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\Helper;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;

class QueryTest extends TestCase
{
    public function testSetAndGetHandler(): void
    {
        $query = new TestQuery();
        $query->setHandler('myhandler');
        $this->assertSame('myhandler', $query->getHandler());
    }

    public function testSetAndGetResultClass(): void
    {
        $query = new TestQuery();
        $query->setResultClass('myResultClass');
        $this->assertSame('myResultClass', $query->getResultClass());
    }

    public function testGetDefaultOmitHeader(): void
    {
        $query = new TestQuery();
        $this->assertNull($query->getOmitHeader());
    }

    public function testSetAndGetOmitHeader(): void
    {
        $query = new TestQuery();
        $query->setOmitHeader(false);
        $this->assertFalse($query->getOmitHeader());
    }

    public function testGetHelper(): void
    {
        $query = new TestQuery();
        $helper = $query->getHelper();
        $this->assertInstanceOf(Helper::class, $helper);
    }

    public function testAddAndGetParams(): void
    {
        $query = new TestQuery();
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 'v2');
        $query->addParam('p2', 'v3'); // should overwrite previous value

        $this->assertSame(
            ['p1' => 'v1', 'p2' => 'v3'],
            $query->getParams()
        );
    }

    public function testAddAndRemoveParam(): void
    {
        $query = new TestQuery();
        $query->addParam('foo', 'bar');
        $this->assertSame('bar', $query->getParams()['foo']);
        $query->removeParam('foo');
        $this->assertEmpty($query->getParams());
    }

    public function testRemoveUnknownParamDoesNotTriggerError(): void
    {
        $query = new TestQuery();
        $query->removeParam('unknown');
        $this->assertEmpty($query->getParams());
    }

    public function testGetDefaultResponseWriter(): void
    {
        $query = new TestQuery();
        $this->assertSame('json', $query->getResponseWriter());
    }

    public function testSetAndGetResponseWriter(): void
    {
        $query = new TestQuery();
        $query->setResponseWriter('phps');
        $this->assertSame('phps', $query->getResponseWriter());
    }

    public function testSetAndGetNow(): void
    {
        $query = new TestQuery();
        $query->setNow(1520997255000);
        $this->assertSame(1520997255000, $query->getNow());
    }

    public function testSetAndGetTimeZone(): void
    {
        $query = new TestQuery();
        $query->setTimeZone(new \DateTimeZone('Europe/Brussels'));
        $this->assertSame('Europe/Brussels', $query->getTimeZone());
    }

    public function testSetAndGetTimeZoneAsString(): void
    {
        $query = new TestQuery();
        $query->setTimeZone('Europe/Brussels');
        $this->assertSame('Europe/Brussels', $query->getTimeZone());
    }

    public function testSetAndGetDistrib(): void
    {
        $query = new TestQuery();
        $query->setDistrib(true);
        $this->assertTrue($query->getDistrib());
    }

    public function testSetAndGetInputEncoding(): void
    {
        $query = new TestQuery();
        $query->setInputEncoding('ISO-8859-1');
        $this->assertSame('ISO-8859-1', $query->getInputEncoding());
    }
}

class TestQuery extends AbstractQuery
{
    public function getType(): string
    {
        return 'testType';
    }

    public function getRequestBuilder(): RequestBuilderInterface
    {
        return null;
    }

    public function getResponseParser(): ResponseParserInterface
    {
        return null;
    }
}
