<?php

namespace Solarium\Tests\Core\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\AbstractRequestBuilder;
use Solarium\QueryType\Select\Query\Query as SelectQuery;

class RequestBuilderTest extends TestCase
{
    /**
     * @var TestRequestBuilder
     */
    protected $builder;

    public function setup()
    {
        $this->builder = new TestRequestBuilder();
    }

    public function testBuild()
    {
        $query = new SelectQuery();
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 'v2');
        $query->setResponseWriter('xyz');
        $request = $this->builder->build($query);

        $this->assertSame(
            'select?omitHeader=true&p1=v1&p2=v2&wt=xyz',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithHeader()
    {
        $query = new SelectQuery();
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 'v2');
        $query->setOmitHeader(false);
        $request = $this->builder->build($query);

        $this->assertSame(
            'select?omitHeader=false&p1=v1&p2=v2&wt=json&json.nl=flat',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithTimeAllowed()
    {
        $query = new SelectQuery();
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 'v2');
        $query->setTimeAllowed(1400);
        $request = $this->builder->build($query);

        $this->assertSame(
            'select?omitHeader=true&timeAllowed=1400&p1=v1&p2=v2&wt=json&json.nl=flat',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithNow()
    {
        $query = new SelectQuery();
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 'v2');
        $query->setNow('1520997255000');
        $request = $this->builder->build($query);

        $this->assertSame(
            'select?omitHeader=true&NOW=1520997255000&p1=v1&p2=v2&wt=json&json.nl=flat',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithTimeZone()
    {
        $query = new SelectQuery();
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 'v2');
        $query->setTimeZone('Europe/Brussels');
        $request = $this->builder->build($query);

        $this->assertSame(
            'select?omitHeader=true&TZ=Europe/Brussels&p1=v1&p2=v2&wt=json&json.nl=flat',
            urldecode($request->getUri())
        );
    }

    public function testRenderLocalParams()
    {
        $myParams = ['tag' => 'mytag', 'ex' => ['exclude1', 'exclude2']];

        $this->assertSame(
            '{!tag=mytag ex=exclude1,exclude2}myValue',
            $this->builder->renderLocalParams('myValue', $myParams)
        );
    }

    public function testRenderLocalParamsWithoutParams()
    {
        $this->assertSame(
            'myValue',
            $this->builder->renderLocalParams('myValue')
        );
    }

    public function testBoolAttribWithNull()
    {
        $this->assertSame(
            '',
            $this->builder->boolAttrib('myattrib', null)
        );
    }

    public function testBoolAttribWithString()
    {
        $this->assertSame(
            ' myattrib="true"',
            $this->builder->boolAttrib('myattrib', 'true')
        );
    }

    public function testBoolAttribWithBool()
    {
        $this->assertSame(
            ' myattrib="false"',
            $this->builder->boolAttrib('myattrib', false)
        );
    }

    public function testAttribWithNull()
    {
        $this->assertSame(
            '',
            $this->builder->attrib('myattrib', null)
        );
    }

    public function testAttribWithString()
    {
        $this->assertSame(
            ' myattrib="myvalue"',
            $this->builder->attrib('myattrib', 'myvalue')
        );
    }
}

class TestRequestBuilder extends AbstractRequestBuilder
{
}
