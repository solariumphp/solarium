<?php

namespace Solarium\Tests\Core\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;

class QueryTest extends TestCase
{
    public function testSetAndGetHandler()
    {
        $query = new TestQuery();
        $query->setHandler('myhandler');
        $this->assertSame('myhandler', $query->getHandler());
    }

    public function testSetAndGetResultClass()
    {
        $query = new TestQuery();
        $query->setResultClass('myResultClass');
        $this->assertSame('myResultClass', $query->getResultClass());
    }

    public function testGetHelper()
    {
        $query = new TestQuery();
        $helper = $query->getHelper();

        $this->assertSame(
            'Solarium\Core\Query\Helper',
            get_class($helper)
        );
    }

    public function testAddAndGetParams()
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

    public function testAddAndRemoveParam()
    {
        $query = new TestQuery();
        $query->addParam('foo', 'bar');
        $this->assertSame('bar', $query->getParams()['foo']);
        $query->removeParam('foo');
        $this->assertEmpty($query->getParams());
    }

    public function testRemoveUnknownParamDoesNotTriggerError()
    {
        $query = new TestQuery();
        $query->removeParam('unknown');
        $this->assertEmpty($query->getParams());
    }

    public function testGetDefaultResponseWriter()
    {
        $query = new TestQuery();
        $this->assertSame('json', $query->getResponseWriter());
    }

    public function testSetAndGetResponseWriter()
    {
        $query = new TestQuery();
        $query->setResponseWriter('phps');
        $this->assertSame('phps', $query->getResponseWriter());
    }

    public function testGetDefaultTimeAllowed()
    {
        $query = new TestQuery();
        $this->assertNull($query->getTimeAllowed());
    }

    public function testSetAndGetTimeAllowed()
    {
        $query = new TestQuery();
        $query->setTimeAllowed(1200);
        $this->assertSame(1200, $query->getTimeAllowed());
    }

    public function testGetDefaultCpuAllowed()
    {
        $query = new TestQuery();
        $this->assertNull($query->getCpuAllowed());
    }

    public function testSetAndGetCpuAllowed()
    {
        $query = new TestQuery();
        $query->setCpuAllowed(500);
        $this->assertSame(500, $query->getCpuAllowed());
    }

    public function testSetAndGetNow()
    {
        $query = new TestQuery();
        $query->setNow(1520997255000);
        $this->assertSame(1520997255000, $query->getNow());
    }

    public function testSetAndGetTimeZone()
    {
        $query = new TestQuery();
        $query->setTimeZone(new \DateTimeZone('Europe/Brussels'));
        $this->assertSame('Europe/Brussels', $query->getTimeZone());
    }

    public function testSetAndGetTimeZoneAsString()
    {
        $query = new TestQuery();
        $query->setTimeZone('Europe/Brussels');
        $this->assertSame('Europe/Brussels', $query->getTimeZone());
    }

    public function testSetAndGetDistrib()
    {
        $query = new TestQuery();
        $query->setDistrib(true);
        $this->assertTrue($query->getDistrib());
    }

    public function testSetAndGetInputEncoding()
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
