<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\RequestBuilder\Spatial as RequestBuilder;
use Solarium\Component\Spatial as Component;
use Solarium\Core\Client\Request;

class SpatialTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setField('geo');
        $component->setDistance(50.1415);
        $component->setPoint('48.2233,16.3161');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'pt' => '48.2233,16.3161',
                'sfield' => 'geo',
                'd' => 50.1415,
            ],
            $request->getParams()
        );
    }
}
