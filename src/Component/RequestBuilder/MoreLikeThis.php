<?php

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\MoreLikeThis as MoreLikeThisComponent;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Add select component morelikethis to the request.
 */
class MoreLikeThis implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for morelikethis.
     *
     * @param MoreLikeThisComponent $component
     * @param Request               $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        // enable morelikethis
        $request->addParam('mlt', 'true');

        $request->addParam('mlt.interestingTerms', $component->getInterestingTerms());
        $request->addParam('mlt.match.include', $component->getMatchInclude());
        $request->addParam('mlt.match.offset', $component->getMatchOffset());
        $request->addParam('mlt.fl', count($component->getFields()) ? implode(',', $component->getFields()) : null);
        $request->addParam('mlt.mintf', $component->getMinimumTermFrequency());
        $request->addParam('mlt.mindf', $component->getMinimumDocumentFrequency());
        $request->addParam('mlt.minwl', $component->getMinimumWordLength());
        $request->addParam('mlt.maxwl', $component->getMaximumWordLength());
        $request->addParam('mlt.maxqt', $component->getMaximumQueryTerms());
        $request->addParam('mlt.maxntp', $component->getMaximumNumberOfTokens());
        $request->addParam('mlt.boost', $component->getBoost());
        $request->addParam(
            'mlt.qf',
            count($component->getQueryFields()) ? $component->getQueryFields() : null
        );
        $request->addParam('mlt.count', $component->getCount());

        return $request;
    }
}
