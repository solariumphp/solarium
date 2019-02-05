<?php

namespace Solarium\Tests\QueryType\Server\Api;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Server\Api\Query;
use Solarium\QueryType\Server\Api\RequestBuilder;

class RequestBuilderTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var RequestBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->query = new Query();
        $this->builder = new RequestBuilder();
    }

    public function testBuildParams()
    {
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertTrue($request->getIsServerRequest());
        $this->assertSame('?wt=json&json.nl=flat', $request->getUri());

        $this->query->setHandler('dummy');
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertTrue($request->getIsServerRequest());
        $this->assertSame('dummy?wt=json&json.nl=flat', $request->getUri());

        $this->query->setAccept('foo/bar');
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertTrue($request->getIsServerRequest());
        $this->assertSame('dummy?wt=json&json.nl=flat', $request->getUri());
        $this->arrayHasKey('Accept: foo/bar', array_flip($request->getHeaders()));

        $this->query->setContentType('foo;bar');
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertTrue($request->getIsServerRequest());
        $this->assertSame('dummy?wt=json&json.nl=flat', $request->getUri());
        $this->arrayHasKey('Accept: foo/bar', array_flip($request->getHeaders()));
        $this->arrayHasKey('Content-Type: foo;bar', array_flip($request->getHeaders()));

        $this->query->setMethod(Request::METHOD_HEAD);
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );
        $this->assertSame(Request::METHOD_HEAD, $request->getMethod());
        $this->assertTrue($request->getIsServerRequest());
        $this->assertSame('dummy?wt=json&json.nl=flat', $request->getUri());
        $this->arrayHasKey('Accept: foo/bar', array_flip($request->getHeaders()));
        $this->arrayHasKey('Content-Type: foo;bar', array_flip($request->getHeaders()));

        $this->query->setMethod(Request::METHOD_POST);
        $this->query->setRawData('some data');
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );
        $this->assertSame(Request::METHOD_POST, $request->getMethod());
        $this->assertTrue($request->getIsServerRequest());
        $this->assertSame('dummy?wt=json&json.nl=flat', $request->getUri());
        $this->arrayHasKey('Accept: foo/bar', array_flip($request->getHeaders()));
        $this->arrayHasKey('Content-Type: foo;bar', array_flip($request->getHeaders()));
        $this->assertSame('some data', $request->getRawData());
    }
}
