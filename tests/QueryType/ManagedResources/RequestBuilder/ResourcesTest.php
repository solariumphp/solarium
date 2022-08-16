<?php

namespace Solarium\Tests\QueryType\ManagedResources\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\Resources as ResourcesQuery;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resources as ResourcesRequestBuilder;

class ResourcesTest extends TestCase
{
    /**
     * @var ResourcesQuery
     */
    protected $query;

    /**
     * @var ResourcesRequestBuilder
     */
    protected $builder;

    public function setUp(): void
    {
        $this->query = new ResourcesQuery();
        $this->builder = new ResourcesRequestBuilder();
    }

    public function testBuild()
    {
        $handler = 'schema/managed';

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
                'omitHeader' => 'true',
            ],
            $request->getParams()
        );

        $this->assertSame($handler, $request->getHandler());
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
    }
}
