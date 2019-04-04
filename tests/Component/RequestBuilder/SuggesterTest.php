<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\RequestBuilder\Suggester as RequestBuilder;
use Solarium\Component\Suggester as Component;
use Solarium\Core\Client\Request;

class SuggesterTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setDictionary('suggest');
        $component->setQuery('ap ip');
        $component->setCount(13);
        $component->setContextFilterQuery('foo bar');
        $component->setBuild(true);
        $component->setReload(false);

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'suggest' => 'true',
                'suggest.dictionary' => ['suggest'],
                'suggest.q' => 'ap ip',
                'suggest.count' => 13,
                'suggest.cfq' => 'foo bar',
                'suggest.build' => 'true',
                'suggest.reload' => 'false',
            ],
            $request->getParams()
        );
    }

    public function testBuildComponentMulipleDictionaries()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setDictionary(['dictionary', 'alt_dictionary']);
        $component->setQuery('ap ip');
        $component->setCount(13);
        $component->setContextFilterQuery('foo bar');
        $component->setBuild(true);
        $component->setReload(false);

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'suggest' => 'true',
                'suggest.dictionary' => ['dictionary', 'alt_dictionary'],
                'suggest.q' => 'ap ip',
                'suggest.count' => 13,
                'suggest.cfq' => 'foo bar',
                'suggest.build' => 'true',
                'suggest.reload' => 'false',
            ],
            $request->getParams()
        );
    }
}
