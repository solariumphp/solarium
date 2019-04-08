<?php

namespace Solarium\Tests\QueryType\Terms;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Terms\Query;
use Solarium\QueryType\Terms\RequestBuilder;

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
        $this->query->setFields('fieldA,fieldB');
        $this->query->setLowerbound('d');
        $this->query->setLowerboundInclude(true);
        $this->query->setMinCount(3);
        $this->query->setMaxCount(100);
        $this->query->setPrefix('de');
        $this->query->setRegex('det.*');
        $this->query->setRegexFlags('case_insensitive,comments');
        $this->query->setLimit(50);
        $this->query->setUpperbound('x');
        $this->query->setUpperboundInclude(false);
        $this->query->setRaw(false);
        $this->query->setSort('index');

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'terms' => 'true',
                'terms.fl' => [
                    'fieldA',
                    'fieldB',
                ],
                'terms.limit' => 50,
                'terms.lower' => 'd',
                'terms.lower.incl' => 'true',
                'terms.maxcount' => 100,
                'terms.mincount' => 3,
                'terms.prefix' => 'de',
                'terms.raw' => 'false',
                'terms.regex' => 'det.*',
                'terms.regex.flag' => [
                    'case_insensitive',
                    'comments',
                ],
                'terms.sort' => 'index',
                'terms.upper' => 'x',
                'terms.upper.incl' => 'false',
                'wt' => 'json',
                'json.nl' => 'flat',
                'omitHeader' => 'true',
            ],
            $request->getParams()
        );

        $this->assertSame(
            Request::METHOD_GET,
            $request->getMethod()
        );
    }
}
