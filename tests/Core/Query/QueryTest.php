<?php

namespace Solarium\Tests\Core\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\AbstractQuery;

class QueryTest extends TestCase
{
    public function testSetAndGetHandler()
    {
        $query = new TestQuery;
        $query->setHandler('myhandler');
        $this->assertSame('myhandler', $query->getHandler());
    }

    public function testSetAndGetResultClass()
    {
        $query = new TestQuery;
        $query->setResultClass('myResultClass');
        $this->assertSame('myResultClass', $query->getResultClass());
    }

    public function testGetHelper()
    {
        $query = new TestQuery;
        $helper = $query->getHelper();

        $this->assertSame(
            'Solarium\Core\Query\Helper',
            get_class($helper)
        );
    }

    public function testAddAndGetParams()
    {
        $query = new TestQuery;
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 'v2');
        $query->addParam('p2', 'v3'); //should overwrite previous value

        $this->assertSame(
            array('p1' => 'v1', 'p2' => 'v3'),
            $query->getParams()
        );
    }

    public function testGetDefaultResponseWriter()
    {
        $query = new TestQuery;
        $this->assertSame('json', $query->getResponseWriter());
    }

    public function testSetAndGetResponseWriter()
    {
        $query = new TestQuery;
        $query->setResponseWriter('phps');
        $this->assertSame('phps', $query->getResponseWriter());
    }

    public function testGetDefaultTimeAllowed()
    {
        $query = new TestQuery;
        $this->assertSame(null, $query->getTimeAllowed());
    }

    public function testSetAndGetTimeAllowed()
    {
        $query = new TestQuery;
        $query->setTimeAllowed(1200);
        $this->assertSame(1200, $query->getTimeAllowed());
    }
}

class TestQuery extends AbstractQuery
{
    public function getType()
    {
        return 'testType';
    }

    public function getRequestBuilder()
    {
        return null;
    }

    public function getResponseParser()
    {
        return null;
    }
}
