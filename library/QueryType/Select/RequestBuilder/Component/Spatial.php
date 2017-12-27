<?php

namespace Solarium\QueryType\Select\RequestBuilder\Component;

use Solarium\QueryType\Select\Query\Component\Spatial as SpatialComponent;
use Solarium\Core\Client\Request;

/**
 * Add select component spatial to the request.
 */
class Spatial implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for Spatial.
     *
     * @param SpatialComponent $component
     * @param Request         $request
     *
     * @return Request
     */
    public function buildComponent($component, $request)
    {
        $request->addParam('sfield', $component->getField());
        $request->addParam('pt', $component->getPoint());
        $request->addParam('d', $component->getDistance());

        return $request;
    }
}
