<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Debug as Component;
use Solarium\Component\RequestBuilder\Debug as RequestBuilder;
use Solarium\Core\Client\Request;

class DebugTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setExplainOther('id:45');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'debugQuery' => 'true',
                'debug.explain.structured' => 'true',
                'explainOther' => 'id:45',
            ],
            $request->getParams()
        );
    }
}
