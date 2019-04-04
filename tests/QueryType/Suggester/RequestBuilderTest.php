<?php

namespace Solarium\Tests\QueryType\Suggester;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Suggester\Query;
use Solarium\QueryType\Suggester\RequestBuilder;

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
        $this->query->setDictionary('suggest');
        $this->query->setQuery('ap ip');
        $this->query->setCount(13);
        $this->query->setContextFilterQuery('foo bar');
        $this->query->setBuild('true');

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'suggest' => 'true',
                'suggest.dictionary' => ['suggest'],
                'suggest.q' => 'ap ip',
                'suggest.count' => 13,
                'suggest.cfq' => 'foo bar',
                'suggest.build' => 'true',
                'suggest.reload' => 'false',
                'wt' => 'json',
                'json.nl' => 'flat',
                'omitHeader' => 'true',
            ],
            $request->getParams()
        );

        $this->assertSame(Request::METHOD_GET, $request->getMethod());
    }
}
