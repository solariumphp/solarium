<?php

namespace Solarium\Tests\QueryType\Select\RequestBuilder\Component;

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

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'rq' => '{!rerank reRankQuery=foo:bar reRankDocs=42 reRankWeight=48.2233}',
            ],
            $request->getParams()
        );
    }
}
