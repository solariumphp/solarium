<?php

namespace Solarium\Tests\QueryType\MoreLikeThis;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\MoreLikeThis\Query;
use Solarium\QueryType\MoreLikeThis\RequestBuilder;

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

    public function setUp()
    {
        $this->query = new Query();
        $this->builder = new RequestBuilder();
    }

    public function testBuildParams()
    {
        $this->query->setInterestingTerms('test');
        $this->query->setMatchInclude(true);
        $this->query->setStart(12);
        $this->query->setMatchOffset(15);
        $this->query->setMltFields('description,name');
        $this->query->setMinimumTermFrequency(1);
        $this->query->setMinimumDocumentFrequency(3);
        $this->query->setMinimumWordLength(2);
        $this->query->setMaximumWordLength(15);
        $this->query->setMaximumQueryTerms(4);
        $this->query->setMaximumNumberOfTokens(5);
        $this->query->setBoost(true);
        $this->query->setQueryFields('description');

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'mlt.interestingTerms' => 'test',
                'mlt.match.include' => 'true',
                'mlt.match.offset' => 15,
                'mlt.fl' => 'description,name',
                'mlt.mintf' => 1,
                'mlt.mindf' => 3,
                'mlt.minwl' => 2,
                'mlt.maxwl' => 15,
                'mlt.maxqt' => 4,
                'mlt.maxntp' => 5,
                'mlt.boost' => 'true',
                'mlt.qf' => ['description'],
                'q' => '*:*',
                'fl' => '*,score',
                'rows' => 10,
                'start' => 12,
                'wt' => 'json',
                'omitHeader' => 'true',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );

        $this->assertSame(Request::METHOD_GET, $request->getMethod());
    }

    public function testBuildWithQueryStream()
    {
        $content = 'test content';

        $this->query->setQuery($content);
        $this->query->setQueryStream(true);

        $request = $this->builder->build($this->query);

        $this->assertSame(Request::METHOD_POST, $request->getMethod());
        $this->assertNull($request->getParam('q'));
        $this->assertSame($content, $request->getRawData());
        $this->assertTrue(in_array('Content-Type: text/plain; charset=utf-8', $request->getHeaders(), true));
    }
}
