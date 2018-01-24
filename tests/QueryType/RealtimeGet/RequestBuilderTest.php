<?php

namespace Solarium\Tests\QueryType\RealtimeGet;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\RealtimeGet\Query;
use Solarium\QueryType\RealtimeGet\RequestBuilder;

class RequestBuilderTest extends TestCase
{
    public function testBuildSingleId()
    {
        $query = new Query();
        $query->addId(123);
        $builder = new RequestBuilder();
        $request = $builder->build($query);

        $this->assertSame(
            $request::METHOD_GET,
            $request->getMethod()
        );

        $this->assertSame(
            'get?omitHeader=true&wt=json&json.nl=flat&ids=123',
            urldecode($request->getUri())
        );
    }

    public function testBuildMultiId()
    {
        $query = new Query();
        $query->addId(123)->addId(456);
        $builder = new RequestBuilder();
        $request = $builder->build($query);

        $this->assertSame(
            $request::METHOD_GET,
            $request->getMethod()
        );

        $this->assertSame(
            'get?omitHeader=true&wt=json&json.nl=flat&ids=123,456',
            urldecode($request->getUri())
        );
    }
}
