<?php

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\DisMax as DismaxComponent;
use Solarium\Core\Client\Request;

/**
 * Add select component dismax to the request.
 */
class DisMax implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for Dismax.
     *
     * @param DismaxComponent $component
     * @param Request         $request
     *
     * @return Request
     */
    public function buildComponent($component, $request)
    {
        // enable dismax
        $request->addParam('defType', $component->getQueryParser());

        $request->addParam('q.alt', $component->getQueryAlternative());
        $request->addParam('qf', $component->getQueryFields());
        $request->addParam('mm', $component->getMinimumMatch());
        $request->addParam('pf', $component->getPhraseFields());
        $request->addParam('ps', $component->getPhraseSlop());
        $request->addParam('qs', $component->getQueryPhraseSlop());
        $request->addParam('tie', $component->getTie());

        // add boostqueries to request
        $boostQueries = $component->getBoostQueries();
        if (0 !== count($boostQueries)) {
            foreach ($boostQueries as $boostQuery) {
                $request->addParam('bq', $boostQuery->getQuery());
            }
        }

        $request->addParam('bf', $component->getBoostFunctions());

        return $request;
    }
}
