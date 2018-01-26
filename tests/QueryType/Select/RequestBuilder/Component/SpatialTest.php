<?php

namespace Solarium\Tests\QueryType\Select\RequestBuilder\Component;

use Solarium\QueryType\Select\RequestBuilder\Component\Spatial as RequestBuilder;
use Solarium\QueryType\Select\Query\Component\Spatial as Component;
use Solarium\Core\Client\Request;

class SpatialTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setField('geo');
        $component->setDistance(50);
        $component->setPoint('48.2233,16.3161');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            array(
                'pt' => '48.2233,16.3161',
                'sfield' => 'geo',
                'd' => 50,
            ),
            $request->getParams()
        );

    }
}
