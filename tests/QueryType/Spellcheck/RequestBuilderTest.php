<?php

namespace Solarium\Tests\QueryType\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Spellcheck\Query;
use Solarium\QueryType\Spellcheck\RequestBuilder;

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
        $this->query->setCollate(true);
        $this->query->setCount(13);
        $this->query->setDictionary('suggest');
        $this->query->setQuery('ap ip');
        $this->query->setOnlyMorePopular(true);
        $this->query->setAlternativeTermCount(5);
        $this->query->setExtendedResults(true);
        $this->query->setAccuracy(0.45);

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'spellcheck' => 'true',
                'spellcheck.q' => 'ap ip',
                'spellcheck.dictionary' => ['suggest'],
                'spellcheck.count' => 13,
                'spellcheck.onlyMorePopular' => 'true',
                'spellcheck.alternativeTermCount' => 5,
                'spellcheck.extendedResults' => 'true',
                'spellcheck.collate' => 'true',
                'spellcheck.build' => 'false',
                'wt' => 'json',
                'json.nl' => 'flat',
                'omitHeader' => 'true',
                'spellcheck.accuracy' => 0.45,
            ],
            $request->getParams()
        );

        $this->assertSame(Request::METHOD_GET, $request->getMethod());
    }
}
