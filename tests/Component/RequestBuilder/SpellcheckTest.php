<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\RequestBuilder\Spellcheck as RequestBuilder;
use Solarium\Component\Spellcheck as Component;
use Solarium\Core\Client\Request;

class SpellcheckTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setQuery('testquery');
        $component->setBuild(false);
        $component->setReload(true);
        $component->setDictionary('testdict');
        $component->setCount(3);
        $component->setOnlyMorePopular(false);
        $component->setAlternativeTermCount(5);
        $component->setExtendedResults(true);
        $component->setCollate(true);
        $component->setMaxCollations(2);
        $component->setMaxCollationTries(4);
        $component->setMaxCollationEvaluations(4);
        $component->setCollateExtendedResults(true);
        $component->setAccuracy(.2);
        $component->setCollateParam('mm', '100%');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'spellcheck' => 'true',
                'spellcheck.q' => 'testquery',
                'spellcheck.build' => 'false',
                'spellcheck.reload' => 'true',
                'spellcheck.dictionary' => ['testdict'],
                'spellcheck.count' => 3,
                'spellcheck.onlyMorePopular' => 'false',
                'spellcheck.alternativeTermCount' => 5,
                'spellcheck.extendedResults' => 'true',
                'spellcheck.collate' => 'true',
                'spellcheck.maxCollations' => 2,
                'spellcheck.maxCollationTries' => 4,
                'spellcheck.maxCollationEvaluations' => 4,
                'spellcheck.collateExtendedResults' => 'true',
                'spellcheck.accuracy' => .2,
                'spellcheck.collateParam.mm' => '100%',
            ],
            $request->getParams()
        );
    }

    public function testBuildComponentMulipleDictionaries()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setQuery('testquery');
        $component->setBuild(false);
        $component->setReload(true);
        $component->setDictionary(['dictionary', 'alt_dictionary']);
        $component->setCount(3);
        $component->setOnlyMorePopular(false);
        $component->setAlternativeTermCount(5);
        $component->setExtendedResults(true);
        $component->setCollate(true);
        $component->setMaxCollations(2);
        $component->setMaxCollationTries(4);
        $component->setMaxCollationEvaluations(4);
        $component->setCollateExtendedResults(true);
        $component->setAccuracy(.2);
        $component->setCollateParam('mm', '100%');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'spellcheck' => 'true',
                'spellcheck.q' => 'testquery',
                'spellcheck.build' => 'false',
                'spellcheck.reload' => 'true',
                'spellcheck.dictionary' => ['dictionary', 'alt_dictionary'],
                'spellcheck.count' => 3,
                'spellcheck.onlyMorePopular' => 'false',
                'spellcheck.alternativeTermCount' => 5,
                'spellcheck.extendedResults' => 'true',
                'spellcheck.collate' => 'true',
                'spellcheck.maxCollations' => 2,
                'spellcheck.maxCollationTries' => 4,
                'spellcheck.maxCollationEvaluations' => 4,
                'spellcheck.collateExtendedResults' => 'true',
                'spellcheck.accuracy' => .2,
                'spellcheck.collateParam.mm' => '100%',
            ],
            $request->getParams()
        );
    }
}
