<?php

namespace Solarium\Component\RequestBuilder;

use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;
use Solarium\Component\SpellcheckInterface;

/**
 * Add select component Spellcheck to the request.
 */
class Spellcheck implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for Spellcheck.
     *
     * @param SpellcheckInterface $component
     * @param Request             $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        // enable spellcheck
        $request->addParam('spellcheck', 'true');

        $request->addParam('spellcheck.q', $component->getQuery());
        $request->addParam('spellcheck.build', $component->getBuild());
        $request->addParam('spellcheck.reload', $component->getReload());
        $request->addParam('spellcheck.dictionary', $component->getDictionary());
        $request->addParam('spellcheck.count', $component->getCount());
        $request->addParam('spellcheck.onlyMorePopular', $component->getOnlyMorePopular());
        $request->addParam('spellcheck.extendedResults', $component->getExtendedResults());
        $request->addParam('spellcheck.collate', $component->getCollate());
        $request->addParam('spellcheck.maxCollations', $component->getMaxCollations());
        $request->addParam('spellcheck.maxCollationTries', $component->getMaxCollationTries());
        $request->addParam('spellcheck.maxCollationEvaluations', $component->getMaxCollationEvaluations());
        $request->addParam('spellcheck.collateExtendedResults', $component->getCollateExtendedResults());
        $request->addParam('spellcheck.accuracy', $component->getAccuracy());

        foreach ($component->getCollateParams() as $param => $value) {
            $request->addParam('spellcheck.collateParam.'.$param, $value);
        }

        return $request;
    }
}
