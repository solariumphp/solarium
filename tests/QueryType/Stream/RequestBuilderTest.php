<?php

namespace Solarium\Tests\QueryType\Stream;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Stream\Query;
use Solarium\QueryType\Stream\RequestBuilder;

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

    public function setUp(): void
    {
        $this->query = new Query();
        $this->builder = new RequestBuilder();
    }

    public function testBuildParams()
    {
        $this->query->setExpression('testexpression');

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'expr' => 'testexpression',
            ],
            $request->getParams()
        );

        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertSame(Request::CONTENT_TYPE_TEXT_PLAIN, $request->getContentType());
        $this->assertSame('stream?expr=testexpression', $request->getUri());
    }
}
