<?php

namespace Solarium\Tests\QueryType\Ping;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Ping\Query;
use Solarium\QueryType\Ping\RequestBuilder;

class RequestBuilderTest extends TestCase
{
    public function testBuild()
    {
        $builder = new RequestBuilder();
        $request = $builder->build(new Query());

        $this->assertSame(
            'admin/ping?omitHeader=false&wt=json&json.nl=flat',
            $request->getUri()
        );

        $this->assertSame(
            Request::METHOD_GET,
            $request->getMethod()
        );
    }
}
