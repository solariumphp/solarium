<?php

namespace Solarium\Tests\QueryType\Schema\Query;

use Solarium\Core\Client\Request;
use Solarium\QueryType\Schema\Query\Query;
use Solarium\QueryType\Schema\RequestBuilder;

class RequestBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var RequestBuilder
     */
    protected $builder;

    protected function setUp()
    {
        $this->query = new Query();
        $this->builder = new RequestBuilder();
    }

    public function testGetMethod()
    {
        $request = $this->builder->build($this->query);
        $this->assertEquals(
            Request::METHOD_GET,
            $request->getMethod()
        );
    }

    public function testGetUri()
    {
        $request = $this->builder->build($this->query);
        $this->assertEquals(
            'schema?omitHeader=false&wt=json&json.nl=flat',
            $request->getUri()
        );
    }

    //TODO: complete individual tests for schema updates
}
