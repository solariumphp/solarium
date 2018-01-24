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
        $component->addFacets(array('facetA', 'facetB'));

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            array(
                'stats' => 'true',
                'stats.facet' => array(
                    'facetA',
                    'facetB',
                ),
                'stats.field' => array(
                    'fieldA',
                    'fieldB',
                ),
                'f.fieldA.stats.facet' => 'fieldFacetA',
            ),
            $request->getParams()
        );
    }
}
