<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\TermVector as Component;
use Solarium\Component\RequestBuilder\TermVector as RequestBuilder;
use Solarium\Core\Client\Request;

class TermVectorTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setDocIds([1, 2]);
        $component->setFields(['fieldA', 'fieldB']);
        $component->setAll(true);
        $component->setDocumentFrequency(true);
        $component->setOffsets(true);
        $component->setPositions(true);
        $component->setPayloads(true);
        $component->setTermFrequency(true);
        $component->setTermFreqInverseDocFreq(true);

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'tv' => 'true',
                'tv.docIds' => [1, 2],
                'tv.fl' => ['fieldA', 'fieldB'],
                'tv.all' => 'true',
                'tv.df' => 'true',
                'tv.offsets' => 'true',
                'tv.positions' => 'true',
                'tv.payloads' => 'true',
                'tv.tf' => 'true',
                'tv.tf_idf' => 'true',
            ],
            $request->getParams()
        );
    }
}
