<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\EdisMax as Component;
use Solarium\Component\RequestBuilder\EdisMax as RequestBuilder;
use Solarium\Core\Client\Request;

class EDisMaxTest extends TestCase
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
        $component->setPhraseBigramFields('content,description,category');
        $component->setPhraseBigramSlop(4);
        $component->setPhraseTrigramFields('content2,date,subcategory');
        $component->setPhraseTrigramSlop(3);
        $component->setQueryPhraseSlop(2);
        $component->setTie(0.5);
        $component->setBoostQuery('cat:1');
        $component->setBoostFunctions('functionX(price)');
        $component->setBoostFunctionsMult('functionX(date)');
        $component->setUserFields('title *_s');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'defType' => 'dummyparser',
                'q.alt' => 'test',
                'qf' => 'content,name',
                'mm' => '75%',
                'pf' => 'content,description',
                'ps' => 1,
                'pf2' => 'content,description,category',
                'ps2' => 4,
                'pf3' => 'content2,date,subcategory',
                'ps3' => 3,
                'qs' => 2,
                'tie' => 0.5,
                'bq' => 'cat:1',
                'bf' => 'functionX(price)',
                'boost' => 'functionX(date)',
                'uf' => 'title *_s',
            ],
            $request->getParams()
        );
    }
}
