<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\QueryElevation as Component;
use Solarium\Component\RequestBuilder\QueryElevation as RequestBuilder;
use Solarium\Core\Client\Request;

class QueryElevationTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setEnableElevation(false);
        $component->setForceElevation(true);
        $component->setExclusive(true);
        $component->setUseConfiguredElevatedOrder(false);
        $component->setMarkExcludes(true);
        $component->setElevateIds(['doc1', 'doc2']);
        $component->setExcludeIds(['doc3', 'doc4']);
        $component->setExcludeTags(['tagA', 'tagB']);

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'fl' => '[elevated],[excluded]',
                'enableElevation' => 'false',
                'forceElevation' => 'true',
                'exclusive' => 'true',
                'useConfiguredElevatedOrder' => 'false',
                'markExcludes' => 'true',
                'elevateIds' => 'doc1,doc2',
                'excludeIds' => 'doc3,doc4',
                'elevate.excludeTags' => 'tagA,tagB',
            ],
            $request->getParams()
        );
    }
}
