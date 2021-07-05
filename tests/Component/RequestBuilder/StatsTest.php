<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\RequestBuilder\Stats as RequestBuilder;
use Solarium\Component\Stats\Stats as Component;
use Solarium\Core\Client\Request;

class StatsTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->createField('fieldA')->addFacet('fieldFacetA');
        $component->createField('fieldB');
        $component->createField('fieldC')->addPivots(['piv1', 'piv2']);
        $component->createField('{!mean=true}fieldD')->addPivots(['piv1', 'piv2']);
        $component->addFacets(['facetA', 'facetB']);

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'stats' => 'true',
                'stats.facet' => [
                    'facetA',
                    'facetB',
                ],
                'stats.field' => [
                    'fieldA',
                    'fieldB',
                    '{!tag=piv1,piv2}fieldC',
                    '{!mean=true tag=piv1,piv2}fieldD',
                ],
                'f.fieldA.stats.facet' => 'fieldFacetA',
            ],
            $request->getParams()
        );
    }
}
