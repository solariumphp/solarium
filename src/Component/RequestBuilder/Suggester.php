<?php

namespace Solarium\Component\RequestBuilder;

use Solarium\Core\Client\Request;
use Solarium\QueryType\Suggester\SuggesterInterface;

/**
 * Add select component Spellcheck to the request.
 */
class Suggester implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for Spellcheck.
     *
     * @param SuggesterInterface $component
     * @param Request            $request
     *
     * @return Request
     */
    public function buildComponent($component, $request)
    {
        $request->addParam('suggest', 'true');
        $request->addParam('suggest.dictionary', $component->getDictionary());
        $request->addParam('suggest.q', $component->getQuery());
        $request->addParam('suggest.count', $component->getCount());
        $request->addParam('suggest.cfq', $component->getContextFilterQuery());
        $request->addParam('suggest.build', $component->getBuild());
        $request->addParam('suggest.reload', $component->getReload());

        return $request;
    }
}
