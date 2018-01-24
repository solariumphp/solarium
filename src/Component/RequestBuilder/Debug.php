<?php

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\Debug as DebugComponent;
use Solarium\Core\Client\Request;

/**
 * Add select component debug to the request.
 */
class Debug implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for the debug component.
     *
     * @param DebugComponent $component
     * @param Request        $request
     *
     * @return Request
     */
    public function buildComponent($component, $request)
    {
        $request->addParam('debugQuery', 'true');
        $request->addParam('debug.explain.structured', 'true');
        $request->addParam('explainOther', $component->getExplainOther());

        return $request;
    }
}
