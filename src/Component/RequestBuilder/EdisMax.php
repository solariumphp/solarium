<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\EdisMax as EdismaxComponent;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Add select component edismax to the request.
 */
class EdisMax implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for EdismaxComponent.
     *
     * @param EdismaxComponent $component
     * @param Request          $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        // enable edismax
        $request->addParam('defType', $component->getQueryParser());

        $request->addParam('q.alt', $component->getQueryAlternative());
        $request->addParam('qf', $component->getQueryFields());
        $request->addParam('mm', $component->getMinimumMatch());
        $request->addParam('pf', $component->getPhraseFields());
        $request->addParam('ps', $component->getPhraseSlop());
        $request->addParam('pf2', $component->getPhraseBigramFields());
        $request->addParam('ps2', $component->getPhraseBigramSlop());
        $request->addParam('pf3', $component->getPhraseTrigramFields());
        $request->addParam('ps3', $component->getPhraseTrigramSlop());
        $request->addParam('qs', $component->getQueryPhraseSlop());
        $request->addParam('tie', $component->getTie());

        // add boostqueries to request
        $boostQueries = $component->getBoostQueries();
        if (0 !== \count($boostQueries)) {
            foreach ($boostQueries as $boostQuery) {
                $request->addParam('bq', $boostQuery->getQuery());
            }
        }

        $request->addParam('bf', $component->getBoostFunctions());
        $request->addParam('boost', $component->getBoostFunctionsMult());
        $request->addParam('uf', $component->getUserFields());

        return $request;
    }
}
