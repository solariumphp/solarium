<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\RequestBuilder\ReRankQuery as RequestBuilder;
use Solarium\Component\ReRankQuery as Component;
use Solarium\Core\Client\Request;

class ReRankQueryTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setQuery('foo:bar');
        $component->setDocs(42);
        $component->setWeight(48.2233);
        $component->setScale('0-1');
        $component->setMainScale('1-5');
        $component->setOperator($component::OPERATOR_MULTIPLY);

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'rq' => '{!rerank reRankQuery=$rqq reRankDocs=42 reRankWeight=48.2233 reRankScale=0-1 reRankMainScale=1-5 reRankOperator=multiply}',
                'rqq' => 'foo:bar',
            ],
            $request->getParams()
        );
    }

    public function testBuildComponentWithRangeQuery()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setQuery('foo:[1 TO *]');
        $component->setDocs(42);
        $component->setWeight(48.2233);
        $component->setScale('0-1');
        $component->setMainScale('1-5');
        $component->setOperator($component::OPERATOR_REPLACE);

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'rq' => '{!rerank reRankQuery=$rqq reRankDocs=42 reRankWeight=48.2233 reRankScale=0-1 reRankMainScale=1-5 reRankOperator=replace}',
                'rqq' => 'foo:[1 TO *]',
            ],
            $request->getParams()
        );
    }
}
