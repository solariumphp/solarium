<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\MoreLikeThis as Component;
use Solarium\Component\RequestBuilder\MoreLikeThis as RequestBuilder;
use Solarium\Core\Client\Request;

class MoreLikeThisTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setFields('description,name');
        $component->setMinimumTermFrequency(1);
        $component->setMinimumDocumentFrequency(3);
        $component->setMaximumDocumentFrequency(6);
        $component->setMaximumDocumentFrequencyPercentage(75);
        $component->setMinimumWordLength(2);
        $component->setMaximumWordLength(15);
        $component->setMaximumQueryTerms(4);
        $component->setMaximumNumberOfTokens(5);
        $component->setBoost(true);
        $component->setQueryFields('description');
        $component->setCount(6);
        $component->setInterestingTerms('test');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'mlt' => 'true',
                'mlt.fl' => 'description,name',
                'mlt.mintf' => 1,
                'mlt.mindf' => 3,
                'mlt.maxdf' => 6,
                'mlt.maxdfpct' => 75,
                'mlt.minwl' => 2,
                'mlt.maxwl' => 15,
                'mlt.maxqt' => 4,
                'mlt.maxntp' => 5,
                'mlt.boost' => 'true',
                'mlt.qf' => ['description'],
                'mlt.count' => 6,
                'mlt.interestingTerms' => 'test',
            ],
            $request->getParams()
        );
    }

    public function testBuildComponentWithoutFieldsAndQueryFields()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setMinimumTermFrequency(1);
        $component->setMinimumDocumentFrequency(3);
        $component->setMaximumDocumentFrequency(6);
        $component->setMaximumDocumentFrequencyPercentage(75);
        $component->setMinimumWordLength(2);
        $component->setMaximumWordLength(15);
        $component->setMaximumQueryTerms(4);
        $component->setMaximumNumberOfTokens(5);
        $component->setBoost(true);
        $component->setCount(6);
        $component->setInterestingTerms('test');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'mlt' => 'true',
                'mlt.mintf' => 1,
                'mlt.mindf' => 3,
                'mlt.maxdf' => 6,
                'mlt.maxdfpct' => 75,
                'mlt.minwl' => 2,
                'mlt.maxwl' => 15,
                'mlt.maxqt' => 4,
                'mlt.maxntp' => 5,
                'mlt.boost' => 'true',
                'mlt.count' => 6,
                'mlt.interestingTerms' => 'test',
            ],
            $request->getParams()
        );
    }
}
