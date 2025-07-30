<?php

namespace Solarium\Tests\QueryType\Luke;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Luke\Query;
use Solarium\QueryType\Luke\RequestBuilder;

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
        $this->query->setShow(Query::SHOW_DOC);
        $this->query->setId('abc');
        $this->query->setDocId(123);
        $this->query->setFields(['id', 'name']);
        $this->query->setNumTerms(15);
        $this->query->setIncludeIndexFieldFlags(false);

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'show' => 'doc',
                'id' => 'abc',
                'docId' => 123,
                'fl' => 'id,name',
                'numTerms' => 15,
                'includeIndexFieldFlags' => 'false',
                'wt' => 'json',
                'omitHeader' => 'true',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );

        $this->assertSame(Request::METHOD_GET, $request->getMethod());
    }

    public function testEmptyBuildParams()
    {
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'omitHeader' => 'true',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );

        $this->assertSame(Request::METHOD_GET, $request->getMethod());
    }
}
