<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\DisMax as Component;
use Solarium\Component\RequestBuilder\DisMax as RequestBuilder;
use Solarium\Core\Client\Request;

class DisMaxTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setQueryParser('dummyparser');
        $component->setQueryAlternative('test');
        $component->setQueryFields('content,name');
        $component->setMinimumMatch('75%');
        $component->setPhraseFields('content,description');
        $component->setPhraseSlop(1);
        $component->setQueryPhraseSlop(2);
        $component->setTie(0.5);
        $component->setBoostQuery('cat:1');
        $component->setBoostFunctions('functionX(price)');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'defType' => 'dummyparser',
                'q.alt' => 'test',
                'qf' => 'content,name',
                'mm' => '75%',
                'pf' => 'content,description',
                'ps' => 1,
                'qs' => 2,
                'tie' => 0.5,
                'bq' => 'cat:1',
                'bf' => 'functionX(price)',
            ],
            $request->getParams()
        );
    }
}
