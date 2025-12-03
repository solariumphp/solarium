<?php

namespace Solarium\Tests\QueryType\Analysis\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Query\Field;
use Solarium\QueryType\Analysis\RequestBuilder\RequestBuilder;

class RequestBuilderTest extends TestCase
{
    protected Field $query;

    protected RequestBuilder $builder;

    public function setUp(): void
    {
        $this->query = new Field();
        $this->builder = new RequestBuilder();
    }

    public function testBuild(): void
    {
        $query = 'cat:1';
        $showMatch = true;
        $handler = 'myhandler';

        $this->query->setQuery($query)
                     ->setShowMatch($showMatch)
                     ->setHandler($handler);
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'analysis.query' => $query,
                'analysis.showmatch' => 'true',
                'json.nl' => 'flat',
                'omitHeader' => 'true',
            ],
            $request->getParams()
        );

        $this->assertSame($handler, $request->getHandler());
    }
}
