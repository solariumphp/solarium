<?php

namespace Solarium\Tests\Core\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\AbstractRequestBuilder;
use Solarium\Core\Query\Helper;
use Solarium\Core\Query\LocalParameters\LocalParameter;
use Solarium\QueryType\Select\Query\Query as SelectQuery;

class RequestBuilderTest extends TestCase
{
    /**
     * @var TestRequestBuilder
     */
    protected $builder;

    public function setUp(): void
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

    public function testBuildWithBoolean()
    {
        $query = new SelectQuery();
        $query->addParam('p1', 'v1');
        $query->addParam('p2', true);
        $query->addParam('p3', false);
        $query->setResponseWriter('xyz');
        $request = $this->builder->build($query);

        $this->assertSame(
            'select?omitHeader=true&p1=v1&p2=true&p3=false&wt=xyz',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithInteger()
    {
        $query = new SelectQuery();
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 1);
        $query->addParam('p3', 0);
        $query->setResponseWriter('xyz');
        $request = $this->builder->build($query);

        $this->assertSame(
            'select?omitHeader=true&p1=v1&p2=1&p3=0&wt=xyz',
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

    public function testBuildWithCpuAllowed()
    {
        $query = new SelectQuery();
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 'v2');
        $query->setCpuAllowed(600);
        $request = $this->builder->build($query);

        $this->assertSame(
            'select?omitHeader=true&cpuAllowed=600&p1=v1&p2=v2&wt=json&json.nl=flat',
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

    public function testBuildWithDistributed()
    {
        $query = new SelectQuery();
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 'v2');
        $query->setDistrib(true);
        $request = $this->builder->build($query);

        $this->assertSame(
            'select?distrib=true&omitHeader=true&p1=v1&p2=v2&wt=json&json.nl=flat',
            urldecode($request->getUri())
        );
    }

    public function testBuildWithInputEncoding()
    {
        $query = new SelectQuery();
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 'v2');
        $query->setInputEncoding('ISO-8859-1');
        $request = $this->builder->build($query);

        $this->assertSame(
            'select?omitHeader=true&ie=ISO-8859-1&p1=v1&p2=v2&wt=json&json.nl=flat',
            urldecode($request->getUri())
        );
    }

    public function testRenderLocalParams()
    {
        $myParams = ['l' => 0, 'u' => 1, 'tag' => 'mytag', 'ex' => ['exclude1', 'exclude2'], 'yes' => true, 'no' => false];

        $this->assertSame(
            '{!l=0 u=1 tag=mytag ex=exclude1,exclude2 yes=true no=false}myValue',
            $this->builder->renderLocalParams('myValue', $myParams)
        );
    }

    public function testRenderLocalParamsWithEscapes()
    {
        $myParams = [
            'as.is' => 'as-is',
            'space' => 'the final frontier',
            'single.quote' => "'60s",
            'double.quote' => '"so-called"',
            'backslash' => ' \ ',
            'curly' => '{x}',
            'list' => ['wax on', 'wax off'],
        ];

        $this->assertSame(
            "{!as.is=as-is space='the final frontier' single.quote='\\'60s' double.quote='\"so-called\"' backslash=' \\\\ ' curly='{x}' list='wax on,wax off'}myValue",
            $this->builder->renderLocalParams('myValue', $myParams)
        );
    }

    public function testRenderLocalParamsWithSplitSmart()
    {
        $splitSmartParam = LocalParameter::IS_SPLIT_SMART[0];

        $myParams = [
            'no.split.smart' => 'a\, b,c',
            $splitSmartParam => 'd\, e,f',
        ];

        $this->assertSame(
            "{!no.split.smart='a\\\\, b,c' $splitSmartParam='d\\, e,f'}myValue",
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

    public function testRenderLocalParamsWithEmptyValue()
    {
        $myParams = ['tag' => 'mytag', 'ex' => ['exclude1', 'exclude2']];

        $this->assertSame(
            '{!tag=mytag ex=exclude1,exclude2}',
            $this->builder->renderLocalParams('', $myParams)
        );
    }

    public function testRenderLocalParamsWithEmptyParams()
    {
        $myParams = ['tag' => 'mytag', 'ex' => [], 'empty' => '', 'null' => null];

        $this->assertSame(
            '{!tag=mytag}myValue',
            $this->builder->renderLocalParams('myValue', $myParams)
        );
    }

    public function testRenderLocalParamsWithParamBlockInValue()
    {
        $myParams = ['tag' => 'mytag', 'ex' => ['exclude1', 'exclude2']];

        $this->assertSame(
            '{!frange u=100 tag=mytag ex=exclude1,exclude2}myValue',
            $this->builder->renderLocalParams('{!frange u=100}myValue', $myParams)
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

    public function testGetHelper()
    {
        $helper = $this->builder->getHelper();

        $this->assertInstanceOf(Helper::class, $helper);
        $this->assertSame($helper, $this->builder->getHelper());
    }
}

class TestRequestBuilder extends AbstractRequestBuilder
{
}
